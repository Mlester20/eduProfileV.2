<?php
session_start();
require_once __DIR__ . '/../../models/teacher/AchievementProfileModel.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class AchievementProfileController extends Controller{
        protected $studentsModel;
        protected $schoolYearModel;
        protected $auditLogs;
        protected $studentService;

        public function __construct($con){
            parent::__construct(
                new AchievementProfileModel($con)
            );
            $this->studentsModel = new StudentsModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->auditLogs = new AuditLogs($con);
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

        public function getStudents(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            $activeSchoolYear = $this->getActiveSchoolYear();
            return $this->studentService->getStudentsByAdviser((int) $_SESSION['id'], $activeSchoolYear['id'] ?? null);
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }

        public function getActiveSchoolYear(){
            $rows = $this->schoolYearModel->getActiveSy();
            return $rows[0] ?? null;
        }

        public function create($data){
            try{
                if(!$this->studentsModel->belongsToAdviser($data['student_id'], $_SESSION['id'])){
                    FlashMessage::setFlash('error', 'You are not authorized to add achievement records for this student.');
                    header('Location: ../../../resources/views/teacher/achievement-profile.php');
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
                    header('Location: ../../../resources/views/teacher/achievement-profile.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to create achievement profile.');
                    header('Location: ../../../resources/views/teacher/achievement-profile.php');
                    exit();
                }
            }catch(Exception $e){
                error_log("Error in AchievementProfileController: " . $e->getMessage());
                FlashMessage::setFlash('error', 'Failed to create achievement profile.');
            }
        }

        public function update($id, $data){
            try{
                if(!$this->studentsModel->belongsToAdviser($data['student_id'], $_SESSION['id'])){
                    FlashMessage::setFlash('error', 'You are not authorized to update achievement records for this student.');
                    header('Location: ../../../resources/views/teacher/achievement-profile.php');
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
                    header('Location: ../../../resources/views/teacher/achievement-profile.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to update achievement profile.');
                    header('Location: ../../../resources/views/teacher/achievement-profile.php');
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
                    header('Location: ../../../resources/views/teacher/achievement-profile.php');
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
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $achievementProfiles = $controller->index(null, $page);
        $students = $controller->getStudents();
        $school_years = $controller->getSchoolYears();
        $activeSchoolYear = $controller->getActiveSchoolYear();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            Csrf::requireValidOnPost('../../../resources/views/teacher/achievement-profile.php');
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
