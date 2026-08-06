<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/teacher/ReadingLevelModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class ReadingLevelController extends Controller{
        protected $auditLogs;
        protected $sy;
        protected $students;
        protected $studentService;

        public function __construct($con){
            parent::__construct(
                new ReadingLevelModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->sy = new SchoolYearModel($con);
            $this->students = new StudentsModel($con);
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
                        'Adding Reading Level',
                        'Reading Level',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Added Reading Level for student ' . $data['student_id']
                    );
                    FlashMessage::setFlash("success", "Reading Level Added Successfully!");
                    header("Location: ../../../resources/views/teacher/reading-level.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again!");
                    header("Location: ../../../resources/views/teacher/reading-level.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error creating reading level " . $e->getMessage());
            }
        }

        public function update($id, $data){
            try{
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating Reading Level',
                        'Reading Level',
                        $id,
                        null,
                        $_SESSION['full_name'] . ' Updated Reading Level for student ' . $data['student_id']
                    );
                    FlashMessage::setFlash("success", "Reading Level Updated Successfully!");
                    header("Location: ../../../resources/views/teacher/reading-level.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/teacher/reading-level.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error updating reading level " . $e->getMessage());
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleting Reading Level',
                        'Reading Level',
                        null,
                        $id,
                        $_SESSION['full_name'] . ' Deleted a Reading Level record'
                    );
                    FlashMessage::setFlash('success', 'Reading level record deleted successfully.');
                    header('Location: ../../../resources/views/teacher/reading-level.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to delete reading level record.');
                    header('Location: ../../../resources/views/teacher/reading-level.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error deleting reading level record: " . $e->getMessage());
            }
        }
    }

    try{
        $controller = new ReadingLevelController($con);
        $filter_student_id = isset($_GET['student_id']) ? (int) $_GET['student_id'] : null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $reading_levels = $controller->index($filter_student_id, $page);
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
            Csrf::requireValidOnPost('../../../resources/views/teacher/reading-level.php');
            if(isset($_POST['create_reading_level'])){
                $controller->create(
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'reading_level' => $_POST['reading_level'],
                        'reading_language' => $_POST['reading_language'],
                        'assessment_date' => $_POST['assessment_date'],
                        'remarks' => $_POST['remarks'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['update_reading_level'])){
                $reading_level_id = $_POST['id'];
                $controller->update(
                    $reading_level_id,
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'reading_level' => $_POST['reading_level'],
                        'reading_language' => $_POST['reading_language'],
                        'assessment_date' => $_POST['assessment_date'],
                        'remarks' => $_POST['remarks'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['delete_reading_level'])){
                $reading_level_id = $_POST['id'];
                $controller->delete($reading_level_id);
            }
        }
    }catch(Exception $e){
        error_log("Error in ReadingLevelController: " . $e->getMessage());
    }
