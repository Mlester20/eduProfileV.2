<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/admin/SubjectsModel.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

if(!in_array($role, ['admin', 'administrative'], true)){
    http_response_code(403);
    exit;
}

AuthRole::allowOnly([$role]);

    class SubjectsController extends Controller{
        protected $auditLogs;
        protected $role;

        public function __construct($con, $role){
            parent::__construct(
                new SubjectsModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->role = $role;
        }

        public function index(){
            return $this->model->index();
        }

        public function getPage($page, $perPage = 10){
            $offset = Paginator::offset($page, $perPage);
            $rows = $this->model->getPage($perPage, $offset);
            $total = $this->model->countAll();
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
        }

        public function create($data){
            try{
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Created Subject',
                        'Subject',
                        null,
                        'Subject',
                        $_SESSION['full_name'] . ' Created Subject ' . $data['subject_name']
                    );
                    FlashMessage::setFlash("success", "Subject created successfully!");
                    header("Location: ../../../resources/views/{$this->role}/subjects.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "That subject already exists for this grade level.");
                    header("Location: ../../../resources/views/{$this->role}/subjects.php");
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
                        'Updated Subject',
                        'Subject',
                        null,
                        'Subject',
                        $_SESSION['full_name'] . ' Updated the Subject ' . $data['subject_name']
                    );
                    FlashMessage::setFlash("success", "Subject updated successfully!");
                    header("Location: ../../../resources/views/{$this->role}/subjects.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/{$this->role}/subjects.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error updating subject " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleted Subject',
                        'Subject',
                        null,
                        'Subject',
                        $_SESSION['full_name'] . ' Deleted a Subject'
                    );
                    FlashMessage::setFlash("success", "Subject deleted successfully!");
                    header("Location: ../../../resources/views/{$this->role}/subjects.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again!");
                    header("Location: ../../../resources/views/{$this->role}/subjects.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error deleting subject " . $e->getMessage());
                return false;
            }
        }
    }


    try{
        $controller = new SubjectsController($con, $role);
        $subjects_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $subjectsPaginated = $controller->getPage($subjects_page);
        $subjects = $subjectsPaginated['data'];
        $gradeLevelsModel = new GradeLevelsModel($con);
        $grade_levels = $gradeLevelsModel->index();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            Csrf::requireValidOnPost("../../../resources/views/{$role}/subjects.php");

            if(isset($_POST['create_subject'])){
                $controller->create(
                    [
                        'grade_level_id' => $_POST['grade_level_id'],
                        'subject_name' => trim($_POST['subject_name']),
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['update_subject'])){
                $subject_id = $_POST['id'];
                $controller->update(
                    $subject_id,
                    [
                        'grade_level_id' => $_POST['grade_level_id'],
                        'subject_name' => trim($_POST['subject_name']),
                        'recorded_by' => $_SESSION['id']
                    ]
                );
            }

            if(isset($_POST['delete_subject'])){
                $subject_id = $_POST['id'];
                $controller->delete($subject_id);
            }
        }
    }catch(Exception $e){
        throw new Exception("Error " . $e->getMessage(), 500);
    }
