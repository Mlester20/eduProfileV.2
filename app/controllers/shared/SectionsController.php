<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/admin/UsersModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

if(!in_array($role, ['admin', 'administrative'], true)){
    http_response_code(403);
    exit;
}

AuthRole::allowOnly([$role]);

    class SectionsController extends Controller{
        protected $auditLogs;
        protected $teachers;
        protected $grade_level;
        protected $role;

        public function __construct($con, $role){
            parent::__construct(
                new SectionsModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->teachers = new UsersModel($con);
            $this->grade_level = new GradeLevelsModel($con);
            $this->role = $role;
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

        public function getAssignmentHistory(){
            $grouped = [];
            foreach($this->model->getAllAssignmentHistory() as $row){
                $grouped[(int) $row['section_id']][] = $row;
            }
            return $grouped;
        }

        public function quickAssignTeacher($sectionId, $teacherId){
            if($sectionId <= 0 || $teacherId <= 0){
                return ['success' => false, 'message' => 'Please choose a teacher.'];
            }

            $conflict = $this->conflictingSectionLabel($teacherId, $sectionId);
            if($conflict !== null){
                return ['success' => false, 'message' => "This teacher is already assigned to {$conflict}."];
            }

            if(!$this->model->updateAdviserOnly($sectionId, $teacherId)){
                return ['success' => false, 'message' => 'Something went wrong, try again.'];
            }

            $this->auditLogs->log(
                $_SESSION['id'] ?? null,
                $_SESSION['role'] ?? 'unknown',
                'Reassigned Teacher',
                'Section',
                $sectionId,
                'Reassigned Teacher',
                ($_SESSION['full_name'] ?? 'Someone') . ' reassigned the teacher for section ID ' . $sectionId
            );

            $history = array_values(array_filter(
                $this->model->getAllAssignmentHistory(),
                function($row) use ($sectionId){ return (int) $row['section_id'] === $sectionId; }
            ));

            return ['success' => true, 'message' => 'Teacher assignment updated.', 'history' => $history];
        }

        /**
         * The Add/Edit Section form lets the admin type a brand-new grade
         * level inline instead of only picking from the existing list
         * (grade_level_id comes through as "__new__" with the typed name in
         * new_grade_name). Reuses an existing grade level by name if one
         * already matches, instead of creating a duplicate.
         */

        /**
         * Human-readable "Grade X - Section" label for the section a teacher
         * already advises, or null if they're free to be assigned. Shared by
         * create(), update(), and quickAssignTeacher() so all three
         * assignment paths enforce "one section per teacher" consistently.
         */

        private function conflictingSectionLabel($teacherId, $excludeSectionId = null){
            if(empty($teacherId)){
                return null;
            }
            $conflict = $this->model->findSectionByAdviser((int) $teacherId, $excludeSectionId);
            if(!$conflict){
                return null;
            }
            return trim(($conflict['grade_level_name'] ?? '') . ' - ' . ($conflict['section_name'] ?? ''));
        }

        private function resolveGradeLevelId($data){
            $gradeLevelId = $data['grade_level_id'] ?? '';
            if($gradeLevelId !== '' && $gradeLevelId !== '__new__'){
                return (int) $gradeLevelId;
            }

            $newName = trim($data['new_grade_name'] ?? '');
            if($newName === ''){
                return null;
            }

            $existing = $this->grade_level->findByName($newName);
            if($existing){
                return (int) $existing['id'];
            }

            $newId = $this->grade_level->create(['grade_name' => $newName]);
            return $newId ? (int) $newId : null;
        }

        public function create($data){
            try{
                $gradeLevelId = $this->resolveGradeLevelId($data);
                if($gradeLevelId === null){
                    FlashMessage::setFlash('error', 'Please select or enter a grade level.');
                    header("Location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }
                $data['grade_level_id'] = $gradeLevelId;

                $conflict = $this->conflictingSectionLabel($data['adviser_id'] ?? null);
                if($conflict !== null){
                    FlashMessage::setFlash('error', "That teacher is already assigned to {$conflict}.");
                    header("Location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }

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
                    header("Location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("Location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }
            }catch(Exception $e) {
                error_log("Error creating section " . $e->getMessage());
                return;
            }
        }

        public function update($id, $data){
            try{
                $gradeLevelId = $this->resolveGradeLevelId($data);
                if($gradeLevelId === null){
                    FlashMessage::setFlash('error', 'Please select or enter a grade level.');
                    header("Location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }
                $data['grade_level_id'] = $gradeLevelId;

                $conflict = $this->conflictingSectionLabel($data['adviser_id'] ?? null, (int) $id);
                if($conflict !== null){
                    FlashMessage::setFlash('error', "That teacher is already assigned to {$conflict}.");
                    header("Location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }

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
                    header("location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again.");
                    header("location: ../../../resources/views/{$this->role}/sections.php");
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
                    header("Location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }else{
                    FlashMessage::setFlash("error", "Something went wrong try again");
                    header("Location: ../../../resources/views/{$this->role}/sections.php");
                    exit();
                }
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }
    }

    try{
        $controller = new SectionsController($con, $role);
        $sections = $controller->index();
        $teachers = $controller->getAvailableTeacher();
        $grade_levels = $controller->getGradeLevel();
        $assignment_history = $controller->getAssignmentHistory();

        if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'quick_assign_teacher'){
            header('Content-Type: application/json');
            Csrf::requireValidJson($_POST['csrf_token'] ?? null);
            echo json_encode($controller->quickAssignTeacher(
                (int) ($_POST['section_id'] ?? 0),
                (int) ($_POST['adviser_id'] ?? 0)
            ));
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            Csrf::requireValidOnPost("../../../resources/views/{$role}/sections.php");
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
