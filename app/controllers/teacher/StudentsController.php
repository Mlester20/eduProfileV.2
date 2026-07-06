<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/teacher/StudentBehavioralProfileModel.php';
require_once __DIR__ . '/../../models/teacher/StudentDevelopmentalProfileModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class StudentsController extends Controller{
        protected $auditLogs;
        protected $sy;
        protected $section;
        protected $service;
        protected $behavioralProfile;
        protected $developmentalProfile;

        public function __construct($con){
            $model = new StudentsModel($con);
            parent::__construct($model);
            $this->service              = new StudentService($con, $model);
            $this->auditLogs            = new AuditLogs($con);
            $this->sy                   = new SchoolYearModel($con);
            $this->section              = new SectionsModel($con);
            $this->behavioralProfile    = new StudentBehavioralProfileModel($con);
            $this->developmentalProfile = new StudentDevelopmentalProfileModel($con);
        }

        public function index(){
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            return $this->service->getPaginatedStudents($page, 10);
        }

        /**
         * Get only active school year
         * @return string
         */

        public function activeSy(){
            return $this->sy->getActiveSy();
        }

        public function getSchoolYears(){
            return $this->sy->index();
        }

        public function getMySections(){
            $teacherId = $_SESSION['id'] ?? null;
            if (!$teacherId) {
                return [];
            }
            return $this->section->findByAdviser($teacherId);
        }

        public function getBehavioralProfiles(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->behavioralProfile->index((int) $_SESSION['id']);
        }

        public function getDevelopmentalProfiles(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->developmentalProfile->index((int) $_SESSION['id']);
        }

        // public function getSectionById($sectionId){
        //     return $this->section->findById($sectionId);
        // }

        public function create($data){
            try{
                if($this->model->isLrnExists($data['lrn'])){
                    FlashMessage::setFlash('error', 'LRN already exists.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unkown',
                        'Adding a new student',
                        'Students',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Added ' . $data['first_name'] . ' ' . $data['last_name']
                    );
                    FlashMessage::setFlash('success', 'Student added successfully.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to add student.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }
            }catch(Exception $e){   
                throw new Exception("Error " . $e->getMessage());
            }
        }

        public function update($id, $data){
            try{
                if($this->model->isLrnExists($data['lrn'], $id)){
                    FlashMessage::setFlash('error', 'LRN already exists.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unkown',
                        'Updating a student',
                        'Students',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Updated a student with ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'Student updated successfully.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to update student.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }
            }catch(Exception $e){
                throw new Exception("Error " . $e->getMessage());
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unkown',
                        'Deleting a student',
                        'Students',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Deleted a student with ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'Student deleted successfully.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to delete student.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }
            }catch(Exception $e){
                throw new Exception("Error " . $e->getMessage());
            }
        }
    }

    try{
        $controller = new StudentsController($con);
        $students = $controller->index();
        $school_years = $controller->getSchoolYears();
        $my_sections = $controller->getMySections();

        $behavior_by_student = [];
        foreach($controller->getBehavioralProfiles() as $record){
            $behavior_by_student[$record['student_id']][] = $record;
        }

        $developmental_by_student = [];
        foreach($controller->getDevelopmentalProfiles() as $record){
            $developmental_by_student[$record['student_id']][] = $record;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(isset($_POST['create_student'])){
                $controller->create(
                    [
                        'lrn' => $_POST['lrn'],
                        'first_name' => $_POST['first_name'],
                        'middle_name' => $_POST['middle_name'],
                        'last_name' => $_POST['last_name'],
                        'suffix' => $_POST['suffix'],
                        'birth_date' => $_POST['birth_date'],
                        'gender' => $_POST['gender'],
                        'address' => $_POST['address'],
                        'school_year_id' => $_POST['school_year_id'],
                        'grade_level_id' => $_POST['grade_level_id'],
                        'section_id' => $_POST['section_id'],
                        'recorded_by' => $_SESSION['id'] ?? null
                    ]
                );
            }
            if(isset($_POST['update_student'])){
                $student_id = $_POST['id'];
                $controller->update(
                    $student_id,
                    [
                        'lrn' => $_POST['lrn'],
                        'first_name' => $_POST['first_name'],
                        'middle_name' => $_POST['middle_name'],
                        'last_name' => $_POST['last_name'],
                        'suffix' => $_POST['suffix'],
                        'birth_date' => $_POST['birth_date'],
                        'gender' => $_POST['gender'],
                        'address' => $_POST['address'],
                        'school_year_id' => $_POST['school_year_id'],
                        'grade_level_id' => $_POST['grade_level_id'],
                        'section_id' => $_POST['section_id'],
                        'recorded_by' => $_SESSION['id'] ?? null
                    ]
                );
            }
            if(isset($_POST['delete_student'])){
                $student_id = $_POST['id'];
                $controller->delete($student_id);
            }
        }
    }catch(Exception $e){
        throw new Exception("Error " . $e->getMessage());
    }