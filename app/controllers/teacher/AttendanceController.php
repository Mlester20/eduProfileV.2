<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/teacher/AttendanceModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class AttendanceController extends Controller{
        protected $auditLogs;
        protected $students;
        protected $studentService;

        public function __construct($con){
            parent::__construct(
                new AttendanceModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->students = new StudentsModel($con);
            $this->studentService = new StudentService($con);
        }

        public function index($student_id = null, $session = null){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->model->index((int) $_SESSION['id'], $student_id, $session);
        }

        public function history($filters = []){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->model->getHistoryGrouped((int) $_SESSION['id'], $filters);
        }

        public function getStudents(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->studentService->getStudentsByAdviser((int) $_SESSION['id']);
        }

        public function gridData($date = null){
            if(!isset($_SESSION['id'])){
                return ['success' => false, 'message' => 'Not authenticated.'];
            }
            if(!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)){
                $date = date('Y-m-d');
            }

            $teacher_id = (int) $_SESSION['id'];
            $school_year_id = $this->model->getActiveSchoolYearId();
            if(!$school_year_id){
                return ['success' => false, 'message' => 'No active school year found. Please contact an administrator.'];
            }

            $students = $this->model->getStudentsForAttendance($teacher_id, $date);

            return [
                'success' => true,
                'date' => $date,
                'school_year_id' => $school_year_id,
                'students' => $students
            ];
        }

        public function saveBulk($data){
            if(!isset($_SESSION['id'])){
                return ['success' => false, 'message' => 'Not authenticated.'];
            }

            $teacher_id = (int) $_SESSION['id'];
            $date = $data['attendance_date'] ?? '';
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)){
                return ['success' => false, 'message' => 'Invalid attendance date.'];
            }

            $records = $data['records'] ?? [];
            if(!is_array($records) || empty($records)){
                return ['success' => false, 'message' => 'No attendance records to save.'];
            }

            $school_year_id = $this->model->getActiveSchoolYearId();
            if(!$school_year_id){
                return ['success' => false, 'message' => 'No active school year found. Please contact an administrator.'];
            }

            $result = $this->model->saveBulk($teacher_id, $date, $school_year_id, $records, $teacher_id);
            if($result === false){
                return ['success' => false, 'message' => 'Something went wrong saving attendance. Please try again.'];
            }

            $this->auditLogs->log(
                $_SESSION['id'] ?? null,
                $_SESSION['role'] ?? 'unknown',
                'Recording Attendance',
                'Attendance',
                null,
                null,
                ($_SESSION['full_name'] ?? 'A teacher') . ' recorded attendance for ' . $date . ' (' . $result['inserted'] . ' records)'
            );

            $message = 'Attendance saved for ' . $result['inserted'] . ' record(s) on ' . $date . '.';
            if($result['skipped'] > 0){
                $message .= ' ' . $result['skipped'] . ($result['skipped'] === 1 ? ' session was' : ' sessions were') . ' already recorded and skipped.';
            }

            return [
                'success' => true,
                'message' => $message,
                'inserted' => $result['inserted'],
                'skipped' => $result['skipped']
            ];
        }

        public function create($data){
            try{
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Recording Attendance',
                        'Attendance',
                        null,
                        null,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' Added Attendance record for ' . $data['student_id']
                    );
                    return true;
                }
                return false;
            }catch(Exception $e){
                error_log("Error creating attendance " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updating Attendance',
                        'Attendance',
                        $id,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' Updated an attendance record ' . $data['student_id']
                    );
                    return true;
                }
                return false;
            }catch(Exception $e){
                error_log("Error updating attendance " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                if($this->model->delete($id)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Deleting Attendance',
                        'Attendance',
                        null,
                        $id,
                        ($_SESSION['full_name'] ?? 'A teacher') . ' Deleted Attendance record '
                    );
                    return true;
                }
                return false;
            }catch(Exception $e){
                error_log("Error deleting attendance record: " . $e->getMessage());
                return false;
            }
        }
    }
