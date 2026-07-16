<?php
session_start();
require_once __DIR__ . '/../../models/administrative/achievementprofilemodel.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class AchievementProfileController extends Controller{
        protected $studentsModel;
        protected $schoolYearModel;
        protected $auditLogs;

        public function __construct($con){
            parent::__construct(
                new AchievementProfileModel($con)
            );
            $this->studentsModel = new StudentsModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        public function index($school_year_id = null, $student_id = null){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->model->index($school_year_id, $student_id);
        }

        public function getStudents(){
            return $this->studentsModel->index();
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }

        public function create($data){
            try{
                if(!$this->studentsModel->exists($data['student_id'])){
                    FlashMessage::setFlash('error', 'You cannot add an achievement record for this student.');
                    header('Location: ../../../resources/views/administrative/achievement-profile.php');
                    exit();
                }
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Adding an achievement profile',
                        'Achievement Profile',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Added an achievement profile for student ID: ' . $data['student_id']
                    );
                    FlashMessage::setFlash('success', 'Achievement profile created successfully.');
                    header('Location: ../../../resources/views/administrative/achievement-profile.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to create achievement profile.');
                    header('Location: ../../../resources/views/administrative/achievement-profile.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error in AchievementProfileController: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Failed to create achievement profile.');
            }
        }

        public function update($id, $data){
            try{
                if(!$this->studentsModel->exists($data['student_id'])){
                    FlashMessage::setFlash('error', 'You cannot update an achievement record for this student.');
                    header('Location: ../../../resources/views/administrative/achievement-profile.php');
                    exit();
                }
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating an achievement profile',
                        'Achievement Profile',
                        $id,
                        null,
                        $_SESSION['full_name'] . ' Updated an achievement profile with ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'Achievement profile updated successfully.');
                    header('Location: ../../../resources/views/administrative/achievement-profile.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to update achievement profile.');
                    header('Location: ../../../resources/views/administrative/achievement-profile.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error in AchievementProfileController: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Failed to update achievement profile.');
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleting an achievement profile',
                        'Achievement Profile',
                        $id,
                        null,
                        $_SESSION['full_name'] . ' Deleted an achievement profile with ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'Achievement profile deleted successfully.');
                    header('Location: ../../../resources/views/administrative/achievement-profile.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error in AchievementProfileController: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Failed to delete achievement profile.');
            }
        }
    }

    try{
        $controller = new AchievementProfileController($con);
        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== '' ? (int) $_GET['school_year_id'] : null;
        $achievementProfiles = $controller->index($school_year_filter);
        $students = $controller->getStudents();
        $school_years = $controller->getSchoolYears();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $achievement_id = $_POST['id'] ?? null;

            if(isset($_POST['add_achievement'])){
                $controller->create(
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'title' => $_POST['title'],
                        'category' => $_POST['category'],
                        'level' => $_POST['level'],
                        'description' => $_POST['description'],
                        'date_received' => $_POST['date_received'],
                        'awarding_body' => $_POST['awarding_body'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['update_achievement']) && $achievement_id !== null){
                $controller->update(
                    $achievement_id,
                    [
                        'student_id' => $_POST['student_id'],
                        'school_year_id' => $_POST['school_year_id'],
                        'title' => $_POST['title'],
                        'category' => $_POST['category'],
                        'level' => $_POST['level'],
                        'description' => $_POST['description'],
                        'date_received' => $_POST['date_received'],
                        'awarding_body' => $_POST['awarding_body'],
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['delete_achievement']) && $achievement_id !== null){
                $controller->delete($achievement_id);
            }
        }
    }catch(Exception $e){
        error_log("Error in AchievementProfileController: " . $e->getMessage());
    }
