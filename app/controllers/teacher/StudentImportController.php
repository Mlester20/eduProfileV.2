<?php
session_start();

require_once __DIR__ . '/../../services/StudentImportService.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['teacher']);

// Template download must happen before any other output.
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['template'])){
    StudentImportService::generateTemplate();
    exit();
}

    class StudentImportController{
        protected $importService;
        protected $auditLogs;

        public function __construct($con){
            $this->importService = new StudentImportService($con);
            $this->auditLogs = new AuditLogs($con);
        }

        public function handleUpload($file, $schoolYearId, $gradeLevelId, $sectionId){
            if(!isset($_SESSION['id'])){
                FlashMessage::setFlash('error', 'Your session has expired. Please log in again.');
                header('Location: ../../../resources/views/teacher/students.php');
                exit();
            }

            if(!isset($file) || $file['error'] !== UPLOAD_ERR_OK){
                FlashMessage::setFlash('error', 'Please choose a valid .xlsx file to upload.');
                header('Location: ../../../resources/views/teacher/students.php');
                exit();
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if($ext !== 'xlsx'){
                FlashMessage::setFlash('error', 'Only .xlsx files are supported. Download the template and fill that in.');
                header('Location: ../../../resources/views/teacher/students.php');
                exit();
            }

            try{
                $rows = $this->importService->parseFile($file['tmp_name']);
                if(empty($rows)){
                    FlashMessage::setFlash('warning', 'No student rows found in the uploaded file.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }

                $result = $this->importService->import($rows, $schoolYearId, $gradeLevelId, $sectionId, (int) $_SESSION['id']);

                $this->auditLogs->log(
                    $_SESSION['id'] ?? null,
                    $_SESSION['role'] ?? 'unknown',
                    'Importing students from Excel',
                    'Students',
                    null,
                    null,
                    $_SESSION['full_name'] . ' Imported ' . $result['imported'] . ' student(s) via Excel'
                );

                $message = "Imported {$result['imported']} student(s).";
                if(!empty($result['errors'])){
                    $shown = array_slice($result['errors'], 0, 10);
                    $message .= ' Skipped ' . count($result['errors']) . ': ' . implode(' ', $shown);
                    if(count($result['errors']) > 10){
                        $message .= ' (+' . (count($result['errors']) - 10) . ' more)';
                    }
                    FlashMessage::setFlash($result['imported'] > 0 ? 'warning' : 'error', $message);
                }else{
                    FlashMessage::setFlash('success', $message);
                }
            }catch(Exception $e){
                error_log("Error importing students: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Could not read that file. Make sure it matches the template format.');
            }

            header('Location: ../../../resources/views/teacher/students.php');
            exit();
        }
    }

    try{
        $controller = new StudentImportController($con);

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_students'])){
            Csrf::requireValidOnPost('../../../resources/views/teacher/students.php');
            $controller->handleUpload(
                $_FILES['import_file'] ?? null,
                $_POST['school_year_id'] ?? null,
                $_POST['grade_level_id'] ?? null,
                $_POST['section_id'] ?? null
            );
        }
    }catch(Exception $e){
        error_log("Error in StudentImportController: " . $e->getMessage());
    }
