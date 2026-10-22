<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/TrashModel.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../helpers/StudentScope.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['teacher']);

    class TrashController extends Controller{
        protected $con;
        protected $studentsModel;
        protected $auditLogs;

        public function __construct($con){
            parent::__construct(
                new TrashModel($con)
            );
            $this->con = $con;
            $this->studentsModel = new StudentsModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        public function index(){
            return [];
        }
        public function create($data){
            return false;
        }
        public function update($id, $data){
            return false;
        }
        public function delete($id){
            return false;
        }

        public function getPage($teacherId, $page, $perPage, $search){
            $offset = Paginator::offset($page, $perPage);
            $rows = $this->model->getPage($perPage, $offset, $teacherId, $search);
            $total = $this->model->countAll($teacherId, $search);
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
        }

        public function restore($id, $teacherId){
            $id = (int) $id;
            if(!$this->model->belongsToOwner($id, $teacherId)){
                return ['success' => false, 'message' => 'You are not authorized to restore this student.'];
            }

            $info = $this->model->getInfo($id);
            if(!$info){
                return ['success' => false, 'message' => 'Student not found.'];
            }
            if($this->studentsModel->isLrnExists($info['lrn'], $info['school_year_id'], $id)){
                return ['success' => false, 'message' => 'Cannot restore: an active student with LRN ' . $info['lrn'] . ' already exists for that school year. Resolve the conflict first.'];
            }

            if($this->studentsModel->restore($id)){
                $name = trim($info['first_name'] . ' ' . ($info['middle_name'] ?? '') . ' ' . $info['last_name'] . ' ' . ($info['suffix'] ?? ''));
                $this->auditLogs->log(
                    $_SESSION['id'] ?? null,
                    $_SESSION['role'] ?? 'unknown',
                    'Restored a student from Trash',
                    'Students',
                    $id,
                    null,
                    ($_SESSION['full_name'] ?? 'A teacher') . ' restored ' . $name . ' from Trash'
                );
                return ['success' => true, 'message' => $name . ' has been restored.'];
            }
            return ['success' => false, 'message' => 'Failed to restore student.'];
        }

        /** Deletes the photo file for a student, if any -- used right before the row itself is gone for good. */
        private function deletePhotoFile($picturePath){
            if(empty($picturePath)) return;
            $fullPath = __DIR__ . '/../../../' . ltrim($picturePath, '/');
            if(is_file($fullPath)){
                @unlink($fullPath);
            }
        }

        public function permanentDelete($id, $teacherId){
            $id = (int) $id;
            if(!$this->model->belongsToOwner($id, $teacherId)){
                return ['success' => false, 'message' => 'You are not authorized to delete this student.'];
            }

            $info = $this->model->getInfo($id);
            $picture = $this->model->getPicturePath($id);
            $name = $info ? trim($info['first_name'] . ' ' . ($info['middle_name'] ?? '') . ' ' . $info['last_name'] . ' ' . ($info['suffix'] ?? '')) : ('ID ' . $id);

            $this->con->begin_transaction();
            try{
                if(!$this->studentsModel->hardDelete($id)){
                    throw new Exception('Delete failed.');
                }
                $this->con->commit();
            }catch(Exception $e){
                $this->con->rollback();
                error_log('Error permanently deleting student: ' . $e->getMessage());
                return ['success' => false, 'message' => 'Failed to permanently delete student.'];
            }

            $this->deletePhotoFile($picture);
            $this->auditLogs->log(
                $_SESSION['id'] ?? null,
                $_SESSION['role'] ?? 'unknown',
                'Permanently deleted a student',
                'Students',
                $id,
                null,
                ($_SESSION['full_name'] ?? 'A teacher') . ' permanently deleted ' . $name . ' (ID ' . $id . ') from Trash'
            );
            return ['success' => true, 'message' => $name . ' has been permanently deleted.'];
        }

        public function bulkRestore(array $ids, $teacherId){
            $restored = [];
            $failed = [];
            foreach($ids as $id){
                $res = $this->restore($id, $teacherId);
                if($res['success']) $restored[] = $id; else $failed[] = ['id' => $id, 'message' => $res['message']];
            }
            return ['success' => true, 'restored' => count($restored), 'failed' => $failed];
        }

        public function bulkPermanentDelete(array $ids, $teacherId){
            $deleted = [];
            $failed = [];
            foreach($ids as $id){
                $res = $this->permanentDelete($id, $teacherId);
                if($res['success']) $deleted[] = $id; else $failed[] = ['id' => $id, 'message' => $res['message']];
            }
            return ['success' => true, 'deleted' => count($deleted), 'failed' => $failed];
        }

        public function emptyTrash($teacherId){
            $ids = $this->model->getTrashedIdsForTeacher($teacherId);
            if(empty($ids)){
                return ['success' => true, 'deleted' => 0];
            }
            $result = $this->bulkPermanentDelete($ids, $teacherId);
            $this->auditLogs->log(
                $_SESSION['id'] ?? null,
                $_SESSION['role'] ?? 'unknown',
                'Emptied Trash',
                'Students',
                null,
                null,
                ($_SESSION['full_name'] ?? 'A teacher') . ' emptied Trash (' . $result['deleted'] . ' student(s) permanently deleted)'
            );
            return $result;
        }

        /**
         * Lightweight auto-purge: runs whenever anyone opens a Trash page
         * (teacher or administrative), rather than needing a real cron/OS
         * scheduler. A student only actually auto-purges the next time
         * *someone's* Trash page loads after their 30 days are up -- not a
         * guaranteed-exact-day background job, but needs nothing beyond
         * what's already running.
         */
        public function purgeExpired(){
            $days = StudentScope::retentionDays();
            $expired = $this->model->getExpired($days);
            $purged = 0;
            foreach($expired as $row){
                $this->con->begin_transaction();
                try{
                    if($this->studentsModel->hardDelete((int) $row['id'])){
                        $this->con->commit();
                        $this->deletePhotoFile($row['picture']);
                        $purged++;
                        $this->auditLogs->log(
                            null,
                            'system',
                            'Auto-purged a student from Trash',
                            'Students',
                            (int) $row['id'],
                            null,
                            'System auto-purged student ID ' . $row['id'] . ' after ' . $days . ' days in Trash'
                        );
                    }else{
                        $this->con->rollback();
                    }
                }catch(Exception $e){
                    $this->con->rollback();
                    error_log('Error auto-purging student: ' . $e->getMessage());
                }
            }
            return $purged;
        }
    }

    try{
        $controller = new TrashController($con);
        $teacherId = (int) ($_SESSION['id'] ?? 0);

        // Auto-purge check, every time Trash (or its API) is reached.
        $controller->purgeExpired();

        $method = $_SERVER['REQUEST_METHOD'];

        if($method === 'GET' && ($_GET['action'] ?? '') !== ''){
            header('Content-Type: application/json');
            // No GET actions currently need JSON -- reserved for future use.
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
            exit();
        }

        if($method === 'POST' && isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')){
            header('Content-Type: application/json');
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            if(!is_array($data)){
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
                exit();
            }
            Csrf::requireValidJson($data['csrf_token'] ?? null);

            switch($data['action'] ?? ''){
                case 'restore':
                    echo json_encode($controller->restore((int) ($data['id'] ?? 0), $teacherId));
                    break;
                case 'permanent_delete':
                    echo json_encode($controller->permanentDelete((int) ($data['id'] ?? 0), $teacherId));
                    break;
                case 'bulk_restore':
                    echo json_encode($controller->bulkRestore(array_map('intval', $data['ids'] ?? []), $teacherId));
                    break;
                case 'bulk_permanent_delete':
                    echo json_encode($controller->bulkPermanentDelete(array_map('intval', $data['ids'] ?? []), $teacherId));
                    break;
                case 'empty_trash':
                    echo json_encode($controller->emptyTrash($teacherId));
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
            }
            exit();
        }

        $trash_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $trash_search = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;
        $trashPaginated = $controller->getPage($teacherId, $trash_page, 10, $trash_search);
    }catch(Exception $e){
        error_log('Error in TrashController: ' . $e->getMessage());
        $trashPaginated = ['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'total_pages' => 1];
        $trash_search = null;
    }
