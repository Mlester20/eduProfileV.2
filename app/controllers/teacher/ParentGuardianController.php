<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/teacher/ParentGuardianModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class ParentGuardianController extends Controller{
        protected $auditLogs;
        protected $studentsModel;

        public function __construct($con){
            parent::__construct(
                new ParentGuardianModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->studentsModel = new StudentsModel($con);
        }

        public function index(){
            return [];
        }

        public function getByStudent($studentId){
            if(!isset($_SESSION['id'])){
                return null;
            }
            return $this->model->getByStudent($studentId, (int) $_SESSION['id']);
        }

        public function upsert($data){
            if(!isset($_SESSION['id'])){
                return ['success' => false, 'message' => 'Not authenticated.'];
            }
            $teacherId = (int) $_SESSION['id'];
            $studentId = (int) ($data['student_id'] ?? 0);

            if(!$this->studentsModel->belongsToAdviser($studentId, $teacherId)){
                return ['success' => false, 'message' => 'You are not authorized to manage this student.'];
            }

            $existing = $this->model->getByStudent($studentId, $teacherId);
            if($existing){
                return $this->update($existing['id'], $data);
            }
            return $this->create($data);
        }

        public function create($data){
            try{
                $data['recorded_by'] = $_SESSION['id'] ?? null;
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Added Parent/Guardian',
                        'Parent/Guardian',
                        null,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' added a parent/guardian record for student ' . $data['student_id']
                    );
                    return ['success' => true, 'message' => 'Parent/guardian information saved successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to save parent/guardian information.'];
            }catch(Exception $e){
                error_log("Error creating parent/guardian record: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong saving parent/guardian information.'];
            }
        }

        public function update($id, $data){
            try{
                $data['recorded_by'] = $_SESSION['id'] ?? null;
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updated Parent/Guardian',
                        'Parent/Guardian',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' updated a parent/guardian record for student ' . $data['student_id']
                    );
                    return ['success' => true, 'message' => 'Parent/guardian information saved successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to save parent/guardian information.'];
            }catch(Exception $e){
                error_log("Error updating parent/guardian record: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong saving parent/guardian information.'];
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleted Parent/Guardian',
                        'Parent/Guardian',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' deleted a parent/guardian record'
                    );
                    return ['success' => true, 'message' => 'Parent/guardian record deleted successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to delete parent/guardian record.'];
            }catch(Exception $e){
                error_log("Error deleting parent/guardian record: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong deleting parent/guardian information.'];
            }
        }
    }

    try{
        $controller = new ParentGuardianController($con);
        $method = $_SERVER['REQUEST_METHOD'];
        header('Content-Type: application/json');

        switch($method){
            case 'GET':
                $studentId = (int) ($_GET['student_id'] ?? 0);
                $record = $controller->getByStudent($studentId);
                echo json_encode(['success' => true, 'data' => $record]);
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

            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        }
    }catch(Exception $e){
        error_log("Error in ParentGuardianController endpoint: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
    }
