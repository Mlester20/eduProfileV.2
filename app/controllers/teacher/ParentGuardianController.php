<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/teacher/ParentGuardianModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class ParentGuardianController extends Controller{
        protected $auditLogs;
        protected $studentsModel;

        public function __construct($con){
            parent::__construct(
                new ParentGuardianModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->studentsModel = new StudentsModel($con);
        }

        public function index(){
            return $this->model->index();
        }

        public function create($data){
            try{
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Added Parent/Guardian',
                        'Parent/Guardian',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Added Parent/Guardian ' . $data['guardian_name']
                    );
                    FlashMessage::setFlash('success', 'Parent/Guardian added successfully.');
                    header("location: ../../../resources/views/teacher/parent-guardian.php");
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to add Parent/Guardian.');
                    header("location: ../../../resources/views/teacher/parent-guardian.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updated Parent/Guardian',
                        'Parent/Guardian',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Updated Parent/Guardian ' . $data['guardian_name']
                    );
                    FlashMessage::setFlash('success', 'Parent/Guardian updated successfully.');
                    header("location: ../../../resources/views/teacher/parent-guardian.php");
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to update Parent/Guardian.');
                    header("location: ../../../resources/views/teacher/parent-guardian.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleted Parent/Guardian',
                        'Parent/Guardian',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Deleted Parent/Guardian'
                    );
                    FlashMessage::setFlash('success', 'Parent/Guardian deleted successfully.');
                    header("location: ../../../resources/views/teacher/parent-guardian.php");
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to delete Parent/Guardian.');
                    header("location: ../../../resources/views/teacher/parent-guardian.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }
    }

    try{
        $controller = new ParentGuardianController($con);
        $parentGuardians = $controller->index();
    }catch(Exception $e){
        error_log("Error " . $e->getMessage());
        die("An error occurred while initializing the controller.");
    }