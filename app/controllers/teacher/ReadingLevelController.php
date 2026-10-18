<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/teacher/ReadingLevelModel.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class ReadingLevelController extends Controller{
        protected $auditLogs;
        protected $students;

        public function __construct($con){
            parent::__construct(
                new ReadingLevelModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->students = new StudentsModel($con);
        }

        public function index($student_id = null){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->model->index((int) $_SESSION['id'], $student_id);
        }

        public function create($data){
            try{
                $teacherId = (int) ($_SESSION['id'] ?? 0);
                if(!$this->students->belongsToAdviser($data['student_id'], $teacherId)){
                    return ['success' => false, 'message' => 'You are not authorized to add reading level records for this student.'];
                }
                $data['recorded_by'] = $teacherId;
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Adding Reading Level',
                        'Reading Level',
                        null,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' added a reading level record for student ' . $data['student_id']
                    );
                    return ['success' => true, 'message' => 'Reading level record added successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to add reading level record.'];
            }catch(Exception $e){
                error_log("Error in ReadingLevelController::create: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong adding the reading level record.'];
            }
        }

        public function update($id, $data){
            try{
                $teacherId = (int) ($_SESSION['id'] ?? 0);
                if(!$this->model->belongsToAdviser($id, $teacherId)){
                    return ['success' => false, 'message' => 'You are not authorized to update this reading level record.'];
                }
                if(!$this->students->belongsToAdviser($data['student_id'], $teacherId)){
                    return ['success' => false, 'message' => 'You are not authorized to update reading level records for this student.'];
                }
                $data['recorded_by'] = $teacherId;
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating Reading Level',
                        'Reading Level',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' updated a reading level record with ID: ' . $id
                    );
                    return ['success' => true, 'message' => 'Reading level record updated successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to update reading level record.'];
            }catch(Exception $e){
                error_log("Error in ReadingLevelController::update: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong updating the reading level record.'];
            }
        }

        public function delete($id){
            try{
                $teacherId = (int) ($_SESSION['id'] ?? 0);
                if(!$this->model->belongsToAdviser($id, $teacherId)){
                    return ['success' => false, 'message' => 'You are not authorized to delete this reading level record.'];
                }
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleting Reading Level',
                        'Reading Level',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' deleted a reading level record with ID: ' . $id
                    );
                    return ['success' => true, 'message' => 'Reading level record deleted successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to delete reading level record.'];
            }catch(Exception $e){
                error_log("Error in ReadingLevelController::delete: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong deleting the reading level record.'];
            }
        }
    }

    try{
        $controller = new ReadingLevelController($con);
        $method = $_SERVER['REQUEST_METHOD'];
        header('Content-Type: application/json');

        switch($method){
            case 'GET':
                $studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : null;
                echo json_encode(['success' => true, 'data' => $controller->index($studentId)]);
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
        error_log("Error in ReadingLevelController endpoint: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
    }
