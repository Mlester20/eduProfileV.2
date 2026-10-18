<?php
session_start();
require_once __DIR__ . '/../../models/administrative/learnerprofilemodel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class LearnerProfileController{
        protected $model;
        protected $sectionsModel;
        protected $schoolYearModel;
        protected $gradeLevelsModel;

        public function __construct($con){
            $this->model = new LearnerProfileModel($con);
            $this->sectionsModel = new SectionsModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->gradeLevelsModel = new GradeLevelsModel($con);
        }

        public function getAllStudents(){
            return $this->model->getAllStudentsForSearch();
        }

        public function getMasterList($page = 1, $status = 'active', $sectionId = null, $schoolYearId = null, $gender = null, $gradeLevelId = null){
            $perPage = 10;
            $offset = Paginator::offset($page, $perPage);
            $rows = $this->model->getPage($perPage, $offset, $status, $sectionId, $schoolYearId, $gender, $gradeLevelId);
            $total = $this->model->countAll($status, $sectionId, $schoolYearId, $gender, $gradeLevelId);
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
        }

        public function getSectionLabel($sectionId){
            if($sectionId === null){
                return null;
            }
            $section = $this->sectionsModel->findById($sectionId);
            if(!$section){
                return null;
            }
            return trim(($section['grade_level_name'] ?? '') . ' - ' . ($section['section_name'] ?? ''));
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }

        public function getGradeLevels(){
            return $this->gradeLevelsModel->index();
        }

        public function getSections($schoolYearId = null, $gradeLevelId = null){
            if($schoolYearId === null){
                return $this->sectionsModel->index($gradeLevelId);
            }
            return $this->sectionsModel->findBySchoolYear($schoolYearId, $gradeLevelId);
        }

        /**
         * Everything the view needs for one student: their info, sibling
         * rows from other school years (same LRN), and all three category
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
                'health' => $this->model->getHealthProfile($studentId),
                'reading_level' => $this->model->getReadingLevelRecords($studentId),
                'parent_guardian' => $this->model->getParentGuardian($studentId),
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
        $section_filter = isset($_GET['section_id']) && $_GET['section_id'] !== '' ? (int) $_GET['section_id'] : null;
        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== '' ? (int) $_GET['school_year_id'] : null;
        $grade_level_filter = isset($_GET['grade_level_id']) && $_GET['grade_level_id'] !== '' ? (int) $_GET['grade_level_id'] : null;
        $gender_filter = in_array($_GET['gender'] ?? '', ['Male', 'Female'], true) ? $_GET['gender'] : null;
        $section_filter_label = $controller->getSectionLabel($section_filter);
        $masterList = $controller->getMasterList($page, $status_filter, $section_filter, $school_year_filter, $gender_filter, $grade_level_filter);
        $school_years = $controller->getSchoolYears();
        $grade_levels = $controller->getGradeLevels();
        $sections = $controller->getSections($school_year_filter, $grade_level_filter);
    }catch(Exception $e){
        error_log("Error in LearnerProfileController: " . $e->getMessage());
        $students = [];
        $profile = null;
        $status_filter = 'active';
        $section_filter = null;
        $school_year_filter = null;
        $grade_level_filter = null;
        $gender_filter = null;
        $section_filter_label = null;
        $masterList = ['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'total_pages' => 1];
        $school_years = [];
        $grade_levels = [];
        $sections = [];
    }
