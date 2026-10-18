<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentHealthModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../services/StudentHealthService.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class StudentHealthController extends Controller{
        protected $auditLogs;
        protected $sy;
        protected $studentService;
        protected $healthService;

        public function __construct($con){
            parent::__construct(
                new StudentHealthModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->sy = new SchoolYearModel($con);
            $this->studentService = new StudentService($con);
            $this->healthService = new StudentHealthService($con);
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

        public function getById($id){
            if(!isset($_SESSION['id'])){
                return null;
            }
            return $this->model->getById($id, (int) $_SESSION['id']);
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

        // Students under this teacher who don't have a health profile yet —
        // used to keep the "Record Health Profile" search from offering
        // students that would collide with the uniq_student_health_profile key.
        public function getStudentsWithoutHealthProfile(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            $teacher_id = (int) $_SESSION['id'];
            $students = $this->getStudents();
            $existingStudentIds = $this->model->getStudentIdsWithHealthProfile($teacher_id);

            return array_values(array_filter($students, function($student) use ($existingStudentIds){
                return !in_array((int) $student['id'], $existingStudentIds, true);
            }));
        }

        public function getByStudent($studentId){
            if(!isset($_SESSION['id'])){
                return null;
            }
            return $this->model->getByStudentId($studentId, (int) $_SESSION['id']);
        }

        public function upsert($data){
            if(!isset($_SESSION['id'])){
                return ['success' => false, 'message' => 'Not authenticated.'];
            }
            $teacher_id = (int) $_SESSION['id'];
            $data['recorded_by'] = $teacher_id;

            $existing = $this->model->getByStudentId((int) ($data['student_id'] ?? 0), $teacher_id);
            if($existing){
                return $this->update($existing['id'], $data);
            }
            return $this->create($data);
        }

        public function create($data){
            if(!isset($_SESSION['id'])){
                return ['success' => false, 'message' => 'Not authenticated.'];
            }
            $teacher_id = (int) $_SESSION['id'];
            $data['recorded_by'] = $teacher_id;

            $result = $this->healthService->create($data, $teacher_id);
            if($result['success']){
                $this->auditLogs->log(
                    $_SESSION['id'] ?? null,
                    $_SESSION['role'] ?? 'unknown',
                    'Recording Student Health',
                    'Student Health',
                    null,
                    null,
                    ($_SESSION['full_name'] ?? 'A teacher') . ' Added Health Profile for ' . $data['student_id']
                );
            }
            return $result;
        }

        public function update($id, $data){
            if(!isset($_SESSION['id'])){
                return ['success' => false, 'message' => 'Not authenticated.'];
            }
            $teacher_id = (int) $_SESSION['id'];
            $data['recorded_by'] = $teacher_id;

            $result = $this->healthService->update($id, $data, $teacher_id);
            if($result['success']){
                $this->auditLogs->log(
                    $_SESSION['id'] ?? null,
                    $_SESSION['role'] ?? 'unknown',
                    'Updating Student Health',
                    'Student Health',
                    $id,
                    null,
                    ($_SESSION['full_name'] ?? 'A teacher') . ' Updated a Health Profile record ' . $data['student_id']
                );
            }
            return $result;
        }

        public function delete($id){
            if(!isset($_SESSION['id'])){
                return ['success' => false, 'message' => 'Not authenticated.'];
            }
            $teacher_id = (int) $_SESSION['id'];

            $result = $this->healthService->delete($id, $teacher_id);
            if($result['success']){
                $this->auditLogs->log(
                    $_SESSION['id'] ?? null,
                    $_SESSION['role'] ?? 'unknown',
                    'Deleting Student Health',
                    'Student Health',
                    null,
                    $id,
                    ($_SESSION['full_name'] ?? 'A teacher') . ' Deleted Health Profile record '
                );
            }
            return $result;
        }
    }

    try{
        $controller = new StudentHealthController($con);
        $method = $_SERVER['REQUEST_METHOD'];
        header('Content-Type: application/json');

        switch($method){
            case 'GET':
                if(isset($_GET['student_id'])){
                    $record = $controller->getByStudent((int) $_GET['student_id']);
                    echo json_encode(['success' => true, 'data' => $record]);
                }else{
                    $student_id = isset($_GET['filter_student_id']) ? (int) $_GET['filter_student_id'] : null;
                    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    echo json_encode(['success' => true, 'data' => $controller->index($student_id, $page)['data']]);
                }
                break;

            case 'POST':
                $raw = file_get_contents('php://input');
                $data = json_decode($raw, true);
                if(!is_array($data)){
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
                    break;
                }
                Csrf::requireValidJson($data['csrf_token'] ?? null);
                echo json_encode($controller->upsert($data));
                break;

            case 'DELETE':
                $raw = file_get_contents('php://input');
                $data = json_decode($raw, true);
                $id = isset($data['id']) ? (int) $data['id'] : (isset($_GET['id']) ? (int) $_GET['id'] : null);
                if(!$id){
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing record id.']);
                    break;
                }
                Csrf::requireValidJson($data['csrf_token'] ?? null);
                echo json_encode($controller->delete($id));
                break;

            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        }
    }catch(Exception $e){
        error_log("Error in StudentHealthController endpoint: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
    }
