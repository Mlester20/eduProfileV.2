<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class GradeLevelsController extends Controller{
        protected $auditLogs;

        public function __construct($con){
            parent::__construct(
                new GradeLevelsModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
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
                        'Grade Level',
                        'Grade Level',
                        null,
                        'Created Grade Level',
                        $_SESSION['full_name'] . ' Created Grade Level ' . $data['grade_name'] 
                    );
                    FlashMessage::setFlash("success", "Grade level created successfully!");
                    header("Location: ../../../resources/views/admin/grade-level.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again!");
                    header("Location: ../../../resources/views/admin/grade-level.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
            }
        }

        public function update($id, $data){
            try{
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Update Grade Level', 
                        'Grade Level',
                        null,
                        $_SESSION['full_name'] . ' Updated the Grade Level ' . $data['grade_name'] 
                    );
                    FlashMessage::setFlash("success", "Grade Level Updated Successfully!");
                    header("Location: ../../../resources/views/admin/grade-level.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/admin/grade-level.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error updating grade level " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleted Grade Level',
                        'Grade Level',
                        null,
                        'Deleted Grade Level',
                        $_SESSION['full_name'] . 'Deleted Grade Level'
                    );
                    FlashMessage::setFlash("success", "Grade level Deleted successfully!");
                    header("Location: ../../../resources/views/admin/grade-level.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again!");
                    header("Location: ../../../resources/views/admin/grade-level.php");
                    exit();    
                }
            }catch(Exception $e){
                error_log("Error deleting grade level " . $e->getMessage());
                return false;
            }
        }
    }


    try{
        $controller = new GradeLevelsController($con);
        $grade_levels = $controller->index();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            if(isset($_POST['create_grade_level'])){
                $controller->create(
                    [
                        'grade_name' => $_POST['grade_name']
                    ]
                );
            }

            if(isset($_POST['update_grade_level'])){
                $grade_level_id = $_POST['id'];
                $controller->update(
                    $grade_level_id,
                    [
                        'grade_name' => $_POST['grade_name']
                    ]
                );
            }

            if(isset($_POST['delete_grade_level'])){
                $grade_leve_id = $_POST['id'];
                $controller->delete($grade_leve_id);
            }
        }
    }catch(Exception $e){
        throw new Exception("Error " . $e->getMessage(), 500);  
    }