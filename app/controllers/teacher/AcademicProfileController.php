<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
require_once __DIR__ . '/../../models/teacher/AcademicProfileModel.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class AcademicProfileController extends Controller{
        protected $studentsModel;
        protected $auditLogs;

        public function __construct($con){
            parent::__construct(
                new AcademicProfileModel($con)
            );
            $this->studentsModel = new StudentsModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        /**
         * Academic Records moved out of the Add/Edit Student modal into
         * its own page (one student can rack up a lot of records across
         * subjects/quarters/years), so this is paginated server-side
         * instead of handing back every row for the student at once.
         */
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

        /**
         * Remarks is never taken from client input -- it's always derived
         * from the grade, per Sir's standing rule: 75 and above is Passed,
         * 74 and below is Failed.
         */
        private function computeRemarks($grade){
            return ((float) $grade) >= 75 ? 'Passed' : 'Failed';
        }

        public function create($data){
            try{
                $teacherId = (int) ($_SESSION['id'] ?? 0);
                if(!$this->studentsModel->belongsToAdviser($data['student_id'], $teacherId)){
                    return ['success' => false, 'message' => 'You are not authorized to add academic records for this student.'];
                }
                $data['recorded_by'] = $teacherId;
                $data['remarks'] = $this->computeRemarks($data['grade'] ?? 0);
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Adding an academic profile',
                        'Academic Profile',
                        null,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' added an academic record for student ' . $data['student_id']
                    );
                    return ['success' => true, 'message' => 'Academic record added successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to add academic record.'];
            }catch(Exception $e){
                error_log("Error in AcademicProfileController::create: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong adding the academic record.'];
            }
        }

        public function update($id, $data){
            try{
                $teacherId = (int) ($_SESSION['id'] ?? 0);
                if(!$this->model->belongsToAdviser($id, $teacherId)){
                    return ['success' => false, 'message' => 'You are not authorized to update this academic record.'];
                }
                if(!$this->studentsModel->belongsToAdviser($data['student_id'], $teacherId)){
                    return ['success' => false, 'message' => 'You are not authorized to update academic records for this student.'];
                }
                $data['recorded_by'] = $teacherId;
                $data['remarks'] = $this->computeRemarks($data['grade'] ?? 0);
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating an academic profile',
                        'Academic Profile',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' updated an academic record with ID: ' . $id
                    );
                    return ['success' => true, 'message' => 'Academic record updated successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to update academic record.'];
            }catch(Exception $e){
                error_log("Error in AcademicProfileController::update: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong updating the academic record.'];
            }
        }

        public function delete($id){
            try{
                $teacherId = (int) ($_SESSION['id'] ?? 0);
                if(!$this->model->belongsToAdviser($id, $teacherId)){
                    return ['success' => false, 'message' => 'You are not authorized to delete this academic record.'];
                }
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleting an academic profile',
                        'Academic Profile',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' deleted an academic record with ID: ' . $id
                    );
                    return ['success' => true, 'message' => 'Academic record deleted successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to delete academic record.'];
            }catch(Exception $e){
                error_log("Error in AcademicProfileController::delete: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong deleting the academic record.'];
            }
        }
    }

    try{
        $controller = new AcademicProfileController($con);
        $method = $_SERVER['REQUEST_METHOD'];
        header('Content-Type: application/json');

        switch($method){
            case 'GET':
                $studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : null;
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                echo json_encode(array_merge(['success' => true], $controller->index($studentId, $page)));
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
                echo json_encode($controller->create($data));
                break;

            case 'PUT':
            case 'PATCH':
                $raw = file_get_contents('php://input');
                $data = json_decode($raw, true);
                if(!is_array($data) || !isset($data['id'])){
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
                    break;
                }
                Csrf::requireValidJson($data['csrf_token'] ?? null);
                echo json_encode($controller->update((int) $data['id'], $data));
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
        error_log("Error in AcademicProfileController endpoint: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
    }
