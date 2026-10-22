<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../../models/admin/SubjectsModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    /**
     * Backs the "Assign Subjects" modal on Manage Teachers -- a teacher's
     * own Academic Records "Subject" dropdown only ever shows what's
     * assigned here (see SubjectsLookupController), not every subject
     * for their grade level. Assignment is per teacher, not per grade
     * level/section: two teachers advising the same grade can end up
     * with different assigned subjects.
     */

    class TeacherSubjectsController{
        protected $subjectsModel;
        protected $sectionsModel;
        protected $auditLogs;

        public function __construct($con){
            $this->subjectsModel = new SubjectsModel($con);
            $this->sectionsModel = new SectionsModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        /**
         * Every subject across every grade level this teacher advises,
         * grouped by grade, each flagged with whether it's currently
         * assigned to them.
         */
        public function getAssignmentOptions($teacherId){
            $sections = $this->sectionsModel->findByAdviser($teacherId);
            $gradeLevelIds = array_values(array_unique(array_map(function($s){
                return (int) $s['grade_level_id'];
            }, $sections)));

            $subjects = $this->subjectsModel->getByGradeLevels($gradeLevelIds);
            $assignedIds = $this->subjectsModel->getAssignedSubjectIds($teacherId);

            foreach($subjects as &$subject){
                $subject['assigned'] = in_array((int) $subject['id'], $assignedIds, true);
            }

            return $subjects;
        }

        public function sync($teacherId, array $subjectIds){
            if($this->subjectsModel->syncTeacherAssignments($teacherId, $subjectIds)){
                $this->auditLogs->log(
                    $_SESSION['id'] ?? null,
                    $_SESSION['role'] ?? 'unknown',
                    'Assigned Subjects',
                    'Subject',
                    $teacherId,
                    'Subject Teacher Assignment',
                    ($_SESSION['full_name'] ?? 'Administrative') . ' updated subject assignments for teacher ID: ' . $teacherId
                );
                return ['success' => true, 'message' => 'Subject assignments updated.'];
            }
            return ['success' => false, 'message' => 'Failed to update subject assignments.'];
        }
    }

    try{
        $controller = new TeacherSubjectsController($con);
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $teacherId = isset($_GET['teacher_id']) ? (int) $_GET['teacher_id'] : 0;
            if(!$teacherId){
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing teacher_id.']);
                exit();
            }
            echo json_encode(['success' => true, 'data' => $controller->getAssignmentOptions($teacherId)]);
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            if(!is_array($data) || empty($data['teacher_id'])){
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
                exit();
            }
            Csrf::requireValidJson($data['csrf_token'] ?? null);
            $subjectIds = array_map('intval', $data['subject_ids'] ?? []);
            echo json_encode($controller->sync((int) $data['teacher_id'], $subjectIds));
            exit();
        }

        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }catch(Exception $e){
        error_log("Error in TeacherSubjectsController endpoint: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
    }
