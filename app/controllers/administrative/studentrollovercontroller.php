<?php
session_start();
require_once __DIR__ . '/../../models/administrative/studentrollovermodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class StudentRolloverController extends Controller{
        protected $schoolYearModel;
        protected $auditLogs;

        public function __construct($con){
            parent::__construct(
                new StudentRolloverModel($con)
            );
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        public function index($schoolYearId = null){
            if($schoolYearId === null){
                return [];
            }
            return $this->model->getActiveStudentsBySchoolYear($schoolYearId);
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }

        public function create($data){
            try{
                $studentIds = $data['student_ids'] ?? [];
                $newSchoolYearId = $data['new_school_year_id'] ?? null;

                if(empty($studentIds) || !$newSchoolYearId){
                    FlashMessage::setFlash('error', 'Select at least one student and a target school year.');
                    header('Location: ../../../resources/views/administrative/student-rollover.php');
                    exit();
                }

                $count = $this->model->rollover($studentIds, $newSchoolYearId, $data['recorded_by']);

                if($count !== false){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Rolling over students to new school year',
                        'Students',
                        null,
                        null,
                        $_SESSION['full_name'] . ' rolled over ' . $count . ' student(s) to a new school year'
                    );
                    FlashMessage::setFlash('success', $count . ' student(s) rolled over successfully.');
                }else{
                    FlashMessage::setFlash('error', 'Failed to roll over students.');
                }
                header('Location: ../../../resources/views/administrative/student-rollover.php');
                exit();
            }catch(Exception $e){
                error_log("Error in StudentRolloverController: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Failed to roll over students.');
            }
        }

        public function update($id, $data){
            FlashMessage::setFlash('error', 'Not supported.');
            header('Location: ../../../resources/views/administrative/student-rollover.php');
            exit();
        }

        public function delete($id){
            FlashMessage::setFlash('error', 'Not supported.');
            header('Location: ../../../resources/views/administrative/student-rollover.php');
            exit();
        }
    }

    try{
        $controller = new StudentRolloverController($con);
        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== '' ? (int) $_GET['school_year_id'] : null;
        $rolloverCandidates = $controller->index($school_year_filter);
        $school_years = $controller->getSchoolYears();

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rollover_students'])){
            $controller->create(
                [
                    'student_ids' => $_POST['student_ids'] ?? [],
                    'new_school_year_id' => $_POST['new_school_year_id'] ?? null,
                    'recorded_by' => $_SESSION['id'] ?? null
                ]
            );
        }
    }catch(Exception $e){
        error_log("Error in StudentRolloverController: " . $e->getMessage());
    }
