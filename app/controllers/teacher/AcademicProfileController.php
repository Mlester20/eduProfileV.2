<?php
session_start();
require_once __DIR__ . '/../../models/teacher/AcademicProfileModel.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class AcademicProfileController extends Controller{
        protected $studentsModel;
        protected $schoolYearModel;
        protected $sy;
        protected $auditLogs;
        protected $studentService;

        public function __construct($con){
            parent::__construct(
                new AcademicProfileModel($con)
            );
            $this->studentsModel = new StudentsModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->sy = new SchoolYearModel($con);
            $this->auditLogs = new AuditLogs($con);
            $this->studentService = new StudentService($con);
        }

        public function index($student_id = null, $session = null){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->model->index((int) $_SESSION['id'], $student_id, $session);
        }

        public function getStudents(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->studentService->getStudentsByAdviser((int) $_SESSION['id']);
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }

        public function create($data){
            try{
                if(!$this->studentsModel->belongsToAdviser($data['student_id'], $_SESSION['id'])){
                    FlashMessage::setFlash('error', 'You are not authorized to add academic records for this student.');
                    header('Location: ../../../resources/views/teacher/academic.php');
                    exit();
                }
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Adding an academic profile',
                        'Academic Profile',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Added an academic profile for student ID: ' . $data['student_id']
                    );
                    FlashMessage::setFlash('success', 'Academic profile created successfully.');
                    header('Location: ../../../resources/views/teacher/academic.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to create academic profile.');
                    header('Location: ../../../resources/views/teacher/academic.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error in AcademicProfileController: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Failed to create academic profile.');
            }
        }

        public function update($id, $data){
            try{
                if(!$this->studentsModel->belongsToAdviser($data['student_id'], $_SESSION['id'])){
                    FlashMessage::setFlash('error', 'You are not authorized to update academic records for this student.');
                    header('Location: ../../../resources/views/teacher/academic.php');
                    exit();
                }
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating an academic profile',
                        'Academic Profile',
                        $id,
                        null,
                        $_SESSION['full_name'] . ' Updated an academic profile with ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'Academic profile updated successfully.');
                    header('Location: ../../../resources/views/teacher/academic.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to update academic profile.');
                    header('Location: ../../../resources/views/teacher/academic.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error in AcademicProfileController: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Failed to update academic profile.');
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleting an academic profile',
                        'Academic Profile',
                        $id,
                        null,
                        $_SESSION['full_name'] . ' Deleted an academic profile with ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'Academic profile deleted successfully.');
                    header('Location: ../../../resources/views/teacher/academic.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error in AcademicProfileController: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Failed to delete academic profile.');
            }
        }
    }

    try{
        $controller = new AcademicProfileController($con);
        $academicProfiles = $controller->index();
        $students = $controller->getStudents();
        $school_years = $controller->getSchoolYears();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $academic_id = $_POST['id'] ?? null;

            if(isset($_POST['add_academic_profile'])){
                $controller->create(
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'subject_name' => $_POST['subject_name'],
                        'grading_period' => $_POST['grading_period'],
                        'grade' => $_POST['grade'],
                        'remarks' => $_POST['remarks'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['update_academic_profile']) && $academic_id !== null){
                $controller->update(
                    $academic_id,
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'subject_name' => $_POST['subject_name'],
                        'grading_period' => $_POST['grading_period'],
                        'grade' => $_POST['grade'],
                        'remarks' => $_POST['remarks'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['delete_academic_profile']) && $academic_id !== null){
                $controller->delete($academic_id);
            }
        }
    }catch(Exception $e){
        error_log("Error in AcademicProfileController: " . $e->getMessage());
    }