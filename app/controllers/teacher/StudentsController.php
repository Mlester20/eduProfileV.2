<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/teacher/ParentGuardianModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/StudentsAge.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class StudentsController extends Controller{
        protected $auditLogs;
        protected $sy;
        protected $section;
        protected $service;
        protected $parentGuardian;

        public function __construct($con){
            $model = new StudentsModel($con);
            parent::__construct($model);
            $this->service              = new StudentService($con, $model);
            $this->auditLogs            = new AuditLogs($con);
            $this->sy                   = new SchoolYearModel($con);
            $this->section              = new SectionsModel($con);
            $this->parentGuardian       = new ParentGuardianModel($con);
        }

        public function index(){
            $teacherId = (int) ($_SESSION['id'] ?? 0);
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $search = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;
            return $this->service->getPaginatedStudents($teacherId, $page, 10, $search);
        }

        /**
         * Get only active school year
         * @return string
         */

        public function activeSy(){
            return $this->sy->getActiveSy();
        }

        public function getMySections(){
            $teacherId = $_SESSION['id'] ?? null;
            if (!$teacherId) {
                return [];
            }
            return $this->section->findByAdviser($teacherId);
        }

        public function getParentGuardians(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->parentGuardian->getByTeacher((int) $_SESSION['id']);
        }

        /**
         * Full (unpaginated) list of this teacher's active advisees, for
         * the Export to Excel button — the paginated index() list isn't
         * suitable since the export should cover the whole roster.
         */

        public function getAllForExport(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->model->index((int) $_SESSION['id']);
        }

        /**
         * One student, scoped to this teacher's own advisees — for the
         * Add/Edit Student modal's tabbed AJAX flow.
         */

        public function getStudent($id){
            if(!isset($_SESSION['id'])){
                return null;
            }
            return $this->model->getById((int) $id, (int) $_SESSION['id']);
        }

        /**
         * Handles the optional "picture" file upload shared by create()/update().
         * Mirrors UpdateProfile.php's profile_picture pattern. Returns the new
         * relative path when a file was uploaded, or null when none was sent
         * (NOT an error — the caller decides whether to keep the old picture).
         */

        private function uploadPicture(){
            if(!isset($_FILES['picture']) || $_FILES['picture']['error'] === UPLOAD_ERR_NO_FILE){
                return ['success' => true, 'uploaded' => false, 'path' => null];
            }
            if($_FILES['picture']['error'] !== UPLOAD_ERR_OK){
                return ['success' => false, 'message' => 'Failed to upload picture.'];
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024;

            if(!in_array($_FILES['picture']['type'], $allowedTypes, true)){
                return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
            }
            if($_FILES['picture']['size'] > $maxSize){
                return ['success' => false, 'message' => 'Picture exceeds the 2MB size limit.'];
            }

            $uploadDir = __DIR__ . '/../../../storage/students/';
            if(!is_dir($uploadDir)){
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
            $fileName = 'student_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $uploadPath = $uploadDir . $fileName;

            if(!move_uploaded_file($_FILES['picture']['tmp_name'], $uploadPath)){
                return ['success' => false, 'message' => 'Failed to save the uploaded picture.'];
            }

            return ['success' => true, 'uploaded' => true, 'path' => 'storage/students/' . $fileName];
        }

        public function create($data){
            try{
                if(!preg_match('/^\d{12}$/', $data['lrn'] ?? '')){
                    return ['success' => false, 'message' => 'LRN must be exactly 12 digits, numbers only.'];
                }
                if($this->model->isLrnExists($data['lrn'], $data['school_year_id'])){
                    return ['success' => false, 'message' => 'LRN already exists for that school year.'];
                }
                if(!empty($data['birth_date']) && StudentsAge::calculateAge($data['birth_date']) > 21){
                    return ['success' => false, 'message' => 'This student is over 21 years old and cannot be recorded.'];
                }

                $upload = $this->uploadPicture();
                if(!$upload['success']){
                    return $upload;
                }
                $data['picture'] = $upload['path'];

                $studentId = $this->model->create($data);
                if($studentId){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Adding a new student',
                        'Students',
                        $studentId,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' added ' . $data['first_name'] . ' ' . $data['last_name']
                    );
                    return ['success' => true, 'message' => 'Student added successfully.', 'student_id' => (int) $studentId];
                }
                return ['success' => false, 'message' => 'Failed to add student.'];
            }catch(Exception $e){
                error_log("Error creating student: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong adding the student.'];
            }
        }

        public function update($id, $data){
            try{
                $teacherId = (int) ($_SESSION['id'] ?? 0);
                $existing = $this->model->getById((int) $id, $teacherId);
                if(!$existing){
                    return ['success' => false, 'message' => 'Student not found.'];
                }
                if(!preg_match('/^\d{12}$/', $data['lrn'] ?? '')){
                    return ['success' => false, 'message' => 'LRN must be exactly 12 digits, numbers only.'];
                }
                if($this->model->isLrnExists($data['lrn'], $data['school_year_id'], $id)){
                    return ['success' => false, 'message' => 'LRN already exists for that school year.'];
                }
                if(!empty($data['birth_date']) && StudentsAge::calculateAge($data['birth_date']) > 21){
                    return ['success' => false, 'message' => 'This student is over 21 years old and cannot be recorded.'];
                }

                $upload = $this->uploadPicture();
                if(!$upload['success']){
                    return $upload;
                }
                $data['picture'] = $upload['uploaded'] ? $upload['path'] : $existing['picture'];

                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating a student',
                        'Students',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' updated a student with ID: ' . $id
                    );
                    return ['success' => true, 'message' => 'Student updated successfully.', 'student_id' => (int) $id];
                }
                return ['success' => false, 'message' => 'Failed to update student.'];
            }catch(Exception $e){
                error_log("Error updating student: " . $e->getMessage());
                return ['success' => false, 'message' => 'Something went wrong updating the student.'];
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unkown',
                        'Deleting a student',
                        'Students',
                        null,
                        null,
                        $_SESSION['full_name'] . ' Deleted a student with ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'Student deleted successfully.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to delete student.');
                    header('Location: ../../../resources/views/teacher/students.php');
                    exit();
                }
            }catch(Exception $e){
                throw new Exception("Error " . $e->getMessage());
            }
        }
    }

    /**
     * Builds the create()/update() payload from a multipart $_POST body —
     * shared by both AJAX actions below so the empty-string-to-null
     * normalization only lives in one place.
     */
    function buildStudentDataFromPost($postData){
        return [
            'lrn' => $postData['lrn'] ?? '',
            'first_name' => $postData['first_name'] ?? '',
            'middle_name' => $postData['middle_name'] ?? '',
            'last_name' => $postData['last_name'] ?? '',
            'suffix' => (($postData['suffix'] ?? '') !== '') ? $postData['suffix'] : null,
            'birth_date' => $postData['birth_date'] ?? '',
            'age_as_of_june' => (($postData['age_as_of_june'] ?? '') !== '') ? $postData['age_as_of_june'] : null,
            'gender' => $postData['gender'] ?? '',
            'mother_tongue' => (($postData['mother_tongue'] ?? '') !== '') ? $postData['mother_tongue'] : null,
            'ip_ethnic_group' => (($postData['ip_ethnic_group'] ?? '') !== '') ? $postData['ip_ethnic_group'] : null,
            'religion' => (($postData['religion'] ?? '') !== '') ? $postData['religion'] : null,
            'house_number' => (($postData['house_number'] ?? '') !== '') ? $postData['house_number'] : null,
            'street' => (($postData['street'] ?? '') !== '') ? $postData['street'] : null,
            'sitio' => (($postData['sitio'] ?? '') !== '') ? $postData['sitio'] : null,
            'purok' => (($postData['purok'] ?? '') !== '') ? $postData['purok'] : null,
            'barangay' => (($postData['barangay'] ?? '') !== '') ? $postData['barangay'] : null,
            'city_municipality' => (($postData['city_municipality'] ?? '') !== '') ? $postData['city_municipality'] : null,
            'province' => (($postData['province'] ?? '') !== '') ? $postData['province'] : null,
            'learning_modality' => (($postData['learning_modality'] ?? '') !== '') ? $postData['learning_modality'] : null,
            'remarks' => (($postData['remarks'] ?? '') !== '') ? $postData['remarks'] : null,
            'school_year_id' => $postData['school_year_id'] ?? null,
            'grade_level_id' => $postData['grade_level_id'] ?? null,
            'section_id' => $postData['section_id'] ?? null,
            'recorded_by' => $_SESSION['id'] ?? null
        ];
    }

    try{
        $controller = new StudentsController($con);
        $method = $_SERVER['REQUEST_METHOD'];

        $isAjaxGet = ($method === 'GET' && ($_GET['action'] ?? '') === 'get');
        $isAjaxPost = ($method === 'POST' && in_array($_POST['action'] ?? '', ['create', 'update'], true));

        if($isAjaxGet || $isAjaxPost){
            header('Content-Type: application/json');

            if($isAjaxGet){
                $student = $controller->getStudent((int) ($_GET['id'] ?? 0));
                echo json_encode(['success' => (bool) $student, 'data' => $student]);
                exit();
            }

            Csrf::requireValidJson($_POST['csrf_token'] ?? null);
            $data = buildStudentDataFromPost($_POST);

            if($_POST['action'] === 'create'){
                echo json_encode($controller->create($data));
            }else{
                $id = (int) ($_POST['id'] ?? 0);
                echo json_encode($controller->update($id, $data));
            }
            exit();
        }

        $students = $controller->index();
        $school_years = $controller->activeSy();
        $my_sections = $controller->getMySections();

        $parent_guardian_by_student = [];
        foreach($controller->getParentGuardians() as $record){
            $parent_guardian_by_student[$record['student_id']] = $record;
        }

        if($method === 'POST' && isset($_POST['delete_student'])){
            Csrf::requireValidOnPost('../../../resources/views/teacher/students.php');
            $controller->delete($_POST['id']);
        }
    }catch(Exception $e){
        throw new Exception("Error " . $e->getMessage());
    }
