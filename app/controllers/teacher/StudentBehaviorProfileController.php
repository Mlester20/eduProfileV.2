<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/teacher/StudentBehavioralProfileModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class StudentBehaviorProfileController extends Controller{
        protected $auditLogs;
        protected $sy;
        protected $students;
        protected $studentService;

        public function __construct($con){
            parent::__construct(
                new StudentBehavioralProfileModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->sy = new SchoolYearModel($con);
            $this->students = new StudentsModel($con);
            $this->studentService = new StudentService($con);
        }

        public function index(){
            return $this->model->index();
        }

        public function getActiveSy(){
            return $this->sy->getActiveSy();
        }

        public function getStudents(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            $activeSy = $this->getActiveSy();
            $activeSchoolYearId = !empty($activeSy) ? $activeSy[0]['id'] : null;
            return $this->studentService->getStudentsByAdviser((int) $_SESSION['id'], $activeSchoolYearId);
        }

        public function create($data){
            try{
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating Student Behavioral',
                        'Student Behavioral',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Added Student Behavioral to ' . $data['student_id']
                    );
                    FlashMessage::setFlash("success", "Student Behavior Added Successfully!");
                    header("Location: ../../../resources/views/teacher/student-behavior.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again!");
                    header("Location: ../../../resources/views/teacher/student-behavior.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error creating student behavioral " . $e->getMessage());
            }         
        }

        public function update($id, $data){
            try{
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating Student Behavior',
                        'Student Behavioral',
                        $id,
                        $_SESSION['full_name'] . ' Updated a student behavioral ' . $data['student_id'] 
                    );
                    FlashMessage::setFlash("success", "Student Behavioral Updated Successfully!");
                    header("Location: ../../../resources/views/teacher/student-behavior.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/teacher/student-behavior.php");
                    exit();                
                }
            }catch(Exception $e){
                error_log("Error updating student " . $e->getMessage());
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleting Student Behavior',
                        'Student Behavioral',
                        null,
                        $id,
                        $_SESSION['full_name'] . ' Deleted Student Behavioral '  
                    );
                    FlashMessage::setFlash('success', 'Student behavioral record deleted successfully.');
                    header('Location: ../../../resources/views/teacher/student-behavior.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error deleting student behavioral record: " . $e->getMessage());
            }
        }
    }

    try{
        $controller = new StudentBehaviorProfileController($con);
        $student_behavioral_profiles = $controller->index();
        $active_sy = $controller->getActiveSy();
        $students = $controller->getStudents();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(isset($_POST['create_student_behavioral'])){
                $controller->create(
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'observation_date' => $_POST['observation_date'],
                        'category' => $_POST['category'],
                        'observation' => $_POST['observation'],
                        'intervention' => $_POST['intervention'],
                        'remarks' => $_POST['remarks'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['update_student_behavioral'])){
                $student_behavioral_id = $_POST['id'];
                $controller->update(
                    $student_behavioral_id,
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'observation_date' => $_POST['observation_date'],
                        'category' => $_POST['category'],
                        'observation' => $_POST['observation'],
                        'intervention' => $_POST['intervention'],
                        'remarks' => $_POST['remarks'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }
        
            if(isset($_POST['delete_behavior_profile'])){
                $behavioral_id = $_POST['id'];
                $controller->delete($behavioral_id);
            }
        }
    }catch(Exception $e){
        error_log("Error in StudentBehaviorProfileController: " . $e->getMessage());
    }