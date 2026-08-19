<?php
session_start();

require_once __DIR__ . '/../../models/admin/SchoolSettingsModel.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['admin']);

    class SchoolSettingsController{
        protected $model;
        protected $auditLogs;

        public function __construct($con){
            $this->model = new SchoolSettingsModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        public function getSettings(){
            return $this->model->get();
        }

        public function update($data){
            try{
                if($this->model->update($data, $_SESSION['id'] ?? null)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updated School Settings',
                        'School Settings',
                        null,
                        null,
                        ($_SESSION['full_name'] ?? 'Admin') . ' updated the school identity settings'
                    );
                    FlashMessage::setFlash("success", "School settings updated successfully!");
                    header("Location: ../../../resources/views/admin/school-settings.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong, try again!");
                    header("Location: ../../../resources/views/admin/school-settings.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error updating school settings " . $e->getMessage());
                FlashMessage::setFlash("error", "Something went wrong, try again!");
                header("Location: ../../../resources/views/admin/school-settings.php");
                exit();
            }
        }

        public function uploadLogo($file){
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024;

            if(!in_array($file['type'], $allowedTypes, true)){
                FlashMessage::setFlash("error", "Invalid file type. Only JPG, PNG, and GIF are allowed.");
                header("Location: ../../../resources/views/admin/school-settings.php");
                exit();
            }

            if($file['size'] > $maxSize){
                FlashMessage::setFlash("error", "Logo file exceeds the 2MB limit.");
                header("Location: ../../../resources/views/admin/school-settings.php");
                exit();
            }

            $destination = __DIR__ . '/../../../public/assets/img/favicon/logo.png';

            if(move_uploaded_file($file['tmp_name'], $destination)){
                $this->auditLogs->log(
                    $_SESSION['id'] ?? null,
                    $_SESSION['role'] ?? 'unknown',
                    'Updated School Logo',
                    'School Settings',
                    null,
                    null,
                    ($_SESSION['full_name'] ?? 'Admin') . ' updated the school logo'
                );
                FlashMessage::setFlash("success", "School logo updated successfully!");
            }else{
                FlashMessage::setFlash("error", "Failed to upload the new logo.");
            }
            header("Location: ../../../resources/views/admin/school-settings.php");
            exit();
        }
    }


    try{
        $controller = new SchoolSettingsController($con);
        $school_settings = $controller->getSettings();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            Csrf::requireValidOnPost('../../../resources/views/admin/school-settings.php');

            if(isset($_POST['update_school_settings'])){
                $controller->update(
                    [
                        'school_name' => trim($_POST['school_name'] ?? ''),
                        'school_id' => trim($_POST['school_id'] ?? ''),
                        'region' => trim($_POST['region'] ?? ''),
                        'division' => trim($_POST['division'] ?? ''),
                        'district' => trim($_POST['district'] ?? ''),
                        'school_head' => trim($_POST['school_head'] ?? ''),
                    ]
                );
            }

            if(isset($_POST['upload_school_logo']) && isset($_FILES['school_logo']) && $_FILES['school_logo']['error'] === UPLOAD_ERR_OK){
                $controller->uploadLogo($_FILES['school_logo']);
            }
        }
    }catch(Exception $e){
        throw new Exception("Error " . $e->getMessage(), 500);
    }
