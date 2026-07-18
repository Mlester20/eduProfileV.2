<?php
session_start();
require_once __DIR__ . '/../../models/administrative/learnerprofilemodel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class LearnerProfileController{
        protected $model;

        public function __construct($con){
            $this->model = new LearnerProfileModel($con);
        }

        public function getAllStudents(){
            return $this->model->getAllStudentsForSearch();
        }

        public function getMasterList($page = 1, $status = 'active'){
            $perPage = 10;
            $offset = Paginator::offset($page, $perPage);
            $rows = $this->model->getPage($perPage, $offset, $status);
            $total = $this->model->countAll($status);
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
        }

        /**
         * Everything the view needs for one student: their info, sibling
         * rows from other school years (same LRN), and all six category
         * tables. Returns null when no student is selected or the id
         * doesn't resolve, so the view can show the empty/search state.
         */

        public function getProfile($studentId){
            if($studentId === null){
                return null;
            }
            $info = $this->model->getStudentInfo($studentId);
            if(!$info){
                return null;
            }
            return [
                'info' => $info,
                'other_years' => $this->model->getOtherYearRecords($info['lrn'], $studentId),
                'academic' => $this->model->getAcademicRecords($studentId),
                'attendance' => $this->model->getAttendanceRecords($studentId),
                'behavioral' => $this->model->getBehavioralRecords($studentId),
                'developmental' => $this->model->getDevelopmentalRecords($studentId),
                'health' => $this->model->getHealthProfile($studentId),
                'achievements' => $this->model->getAchievementRecords($studentId),
            ];
        }
    }

    try{
        $controller = new LearnerProfileController($con);
        $students = $controller->getAllStudents();
        $selected_student_id = isset($_GET['student_id']) && $_GET['student_id'] !== '' ? (int) $_GET['student_id'] : null;
        $profile = $controller->getProfile($selected_student_id);
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $status_filter = in_array($_GET['status'] ?? '', ['active', 'archived'], true) ? $_GET['status'] : 'active';
        $masterList = $controller->getMasterList($page, $status_filter);
    }catch(Exception $e){
        error_log("Error in LearnerProfileController: " . $e->getMessage());
        $students = [];
        $profile = null;
        $status_filter = 'active';
        $masterList = ['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'total_pages' => 1];
    }
