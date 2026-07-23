<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/admin/UsersModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class SectionsController extends Controller{
        protected $auditLogs;
        protected $teachers;
        protected $grade_level;

        public function __construct($con){
            parent::__construct(
                new SectionsModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->teachers = new UsersModel($con);
            $this->grade_level = new GradeLevelsModel($con);
        }

        public function index(){
            return $this->model->index();
        }

        public function getGradeLevel(){
            return $this->grade_level->index();
        }

        public function getAvailableTeacher(){
            return $this->teachers->getAvailableTeachers();
        }

        public function create($data){
            try{
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Adding new section',
                        'Section',
                        null,
                        'Created Grade Level',
                        $_SESSION['full_name'] . ' Created new section ' . $data['section_name']
                    );
                    FlashMessage::setFlash("success", "Section created successfully!");
                    header("Location: ../../../resources/views/administrative/sections.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/administrative/sections.php");
                    exit();
                }
            }catch(Exception $e) {
                error_log("Error creating section " . $e->getMessage());
                return;
            }
        }

        public function update($id, $data){
            try{
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating Section',
                        'Section',
                        $id,
                        'Updating Section',
                        $_SESSION['full_name'] . ' Updating the ' . $data['section_name']
                    );
                    FlashMessage::setFlash("success", "Section updated successfully!");
                    header("location: ../../../resources/views/administrative/sections.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("location: ../../../resources/views/administrative/sections.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error updating section " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'uknown',
                        'Deleted Section',
                        'Section',
                        $id,
                        $_SESSION['full_name'] . 'Deleted Section'
                    );
                    FlashMessage::setFlash("success", "Section Deleted Successfully!");
                    header("Location: ../../../resources/views/administrative/sections.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again");
                    header("Location: ../../../resources/views/administrative/sections.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }
    }

    try{
        $controller = new SectionsController($con);
        $sections = $controller->index();
        $teachers = $controller->getAvailableTeacher();
        $grade_levels = $controller->getGradeLevel();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            Csrf::requireValidOnPost('../../../resources/views/administrative/sections.php');
            if(isset($_POST['create_section'])){
                $controller->create(
                    [
                        'grade_level_id' => $_POST['grade_level_id'],
                        'section_name' => $_POST['section_name'],
                        'adviser_id' => $_POST['adviser_id']
                    ]
                );
            }
            if(isset($_POST['update_section'])){
                $section_id = $_POST['id'];
                $controller->update(
                    $section_id,
                    [
                        'grade_level_id' => $_POST['grade_level_id'],
                        'section_name' => $_POST['section_name'],
                        'adviser_id' => $_POST['adviser_id']
                    ]
                );
            }
            if(isset($_POST['delete_section'])){
                $section_id = $_POST['id'];
                $controller->delete($section_id);
            }
        }
    }catch(Exception $e){
        throw new Exception("Error " . $e->getMessage());
    }
