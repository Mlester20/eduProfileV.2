<?php
session_start();
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/PastRecordsModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['teacher']);

    class PastRecordsController{
        protected $model;

        public function __construct($con){
            $this->model = new PastRecordsModel($con);
        }

        public function getMasterList($page = 1, $schoolYearId = null){
            $perPage = 10;
            if(!isset($_SESSION['id'])){
                return array_merge(['data' => []], Paginator::meta(0, $page, $perPage));
            }
            $teacherId = (int) $_SESSION['id'];
            $offset = Paginator::offset($page, $perPage);
            $rows = $this->model->getStudentsPage($teacherId, $perPage, $offset, $schoolYearId);
            $total = $this->model->countStudents($teacherId, $schoolYearId);
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
        }

        public function getSchoolYears(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->model->getArchivedSchoolYears((int) $_SESSION['id']);
        }

        /**
         * Everything for one archived student at once: their info plus all
         * seven category tables — the teacher-scoped counterpart to
         * LearnerProfileController::getProfile(). Returns null when no
         * student is selected or the id doesn't resolve to one of this
         * teacher's own archived students.
         */

        public function getProfile($studentId){
            if($studentId === null || !isset($_SESSION['id'])){
                return null;
            }
            $teacherId = (int) $_SESSION['id'];
            $info = $this->model->getStudentInfo($teacherId, $studentId);
            if(!$info){
                return null;
            }
            return [
                'info' => $info,
                'academic' => $this->model->getAcademicRecords($teacherId, $studentId),
                'behavioral' => $this->model->getBehavioralRecords($teacherId, $studentId),
                'developmental' => $this->model->getDevelopmentalRecords($teacherId, $studentId),
                'health' => $this->model->getHealthProfile($teacherId, $studentId),
                'attendance' => $this->model->getAttendanceRecords($teacherId, $studentId),
                'achievements' => $this->model->getAchievementRecords($teacherId, $studentId),
                'reading_level' => $this->model->getReadingLevelRecords($teacherId, $studentId),
                'parent_guardian' => $this->model->getParentGuardian($studentId),
            ];
        }
    }

    try{
        $controller = new PastRecordsController($con);

        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== '' ? (int) $_GET['school_year_id'] : null;
        $selected_student_id = isset($_GET['student_id']) && $_GET['student_id'] !== '' ? (int) $_GET['student_id'] : null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        $school_years = $controller->getSchoolYears();
        $profile = $controller->getProfile($selected_student_id);
        $masterList = $controller->getMasterList($page, $school_year_filter);
    }catch(Exception $e){
        error_log("Error in PastRecordsController: " . $e->getMessage());
        $school_years = [];
        $profile = null;
        $masterList = ['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'total_pages' => 1];
    }
