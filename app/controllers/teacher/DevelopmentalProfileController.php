<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/teacher/StudentDevelopmentalProfileModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class DevelopmentalProfileController extends Controller{
        protected $auditLogs;
        protected $sy;
        protected $students;
        protected $studentService;

        public function __construct($con){
            parent::__construct(
                new StudentDevelopmentalProfileModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->sy = new SchoolYearModel($con);
            $this->studentService = new StudentService($con);
        }

        public function index($student_id = null, $page = 1){
            $perPage = 10;
            if(!isset($_SESSION['id'])){
                return array_merge(['data' => []], Paginator::meta(0, $page, $perPage));
            }
            $teacherId = (int) $_SESSION['id'];
            $offset = Paginator::offset($page, $perPage);
            $rows = $this->model->getPage($teacherId, $perPage, $offset, $student_id);
            $total = $this->model->countAll($teacherId, $student_id);
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
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
                        'Creating new Developmental Profile',
                        'Developmental',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Created student developmental for ' . $data['id']   
                    );
                    FlashMessage::setFlash("success", "Student Developmental Created Successfully!");
                    header("Location: ../../../resources/views/teacher/student-developmental.php");
                    exit();                
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/teacher/student-developmental.php");
                    exit();                
                }
            }catch(Exception $e){
                error_log("Error creating developmental " . $e->getMessage());
            }
        }

        public function update($id, $data){
            try{
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating Developmental Profile',
                        'Developmental',
                        $id,
                        null,
                        $_SESSION['full_name'] . ' updating student developmental for ' . $data['id']   
                    );
                    FlashMessage::setFlash("success", "Student Developmental Updated Successfully!");
                    header("Location: ../../../resources/views/teacher/student-developmental.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/teacher/student-developmental.php");
                    exit();                
                }
            }catch(Exception $e){
                error_log("Error updating developmental profile " . $e->getMessage());
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleting Developmental Profile',
                        'Developmental',
                        $id,
                        null,
                        $_SESSION['full_name'] . ' Deleted student developmental for '   
                    );
                    FlashMessage::setFlash("success", "Student Developmental Deleted Successfully!");
                    header("Location: ../../../resources/views/teacher/student-developmental.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/teacher/student-developmental.php");
                    exit();                
                }                
            }catch(Exception $e){
                error_log("Error deleting development profile " . $e->getMessage());
            }
        }
    }

    try{
        $controller = new DevelopmentalProfileController($con);
        $filter_student_id = isset($_GET['student_id']) ? (int) $_GET['student_id'] : null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $developmentals = $controller->index($filter_student_id, $page);
        $active_sy = $controller->getActiveSy();
        $students = $controller->getStudents();
        $filtered_student = null;
        if($filter_student_id !== null){
            foreach($students as $s){
                if((int) $s['id'] === $filter_student_id){
                    $filtered_student = $s;
                    break;
                }
            }
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            Csrf::requireValidOnPost('../../../resources/views/teacher/student-developmental.php');
            if(isset($_POST['create_developmental_profile'])){
                $controller->create(
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'domain' => $_POST['domain'],
                        'observation' => $_POST['observation'],
                        'recommendation' => $_POST['recommendation'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['update_developmental_profile'])){
                $developmental_id = $_POST['id'];
                $controller->update(
                    $developmental_id,
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'domain' => $_POST['domain'],
                        'observation' => $_POST['observation'],
                        'recommendation' => $_POST['recommendation'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['delete_developmental'])){
                $developmental_id = $_POST['id'];
                $controller->delete($developmental_id);
            }
        }
    }catch(Exception $e){
        error_log("Error while initializing the controller " . $e->getMessage());
    }