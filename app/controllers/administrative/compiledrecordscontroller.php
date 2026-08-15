<?php
session_start();
require_once __DIR__ . '/../../models/administrative/compiledrecordsmodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class CompiledRecordsController{
        protected $model;
        protected $schoolYearModel;
        protected $sectionsModel;
        protected $gradeLevelsModel;

        const CATEGORIES = ['Academic', 'Behavioral', 'Developmental', 'Health', 'Attendance', 'Achievements'];

        public function __construct($con){
            $this->model = new CompiledRecordsModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->sectionsModel = new SectionsModel($con);
            $this->gradeLevelsModel = new GradeLevelsModel($con);
        }

        public function index($category, $schoolYearId = null, $sectionId = null, $gradeLevelId = null){
            switch($category){
                case 'Behavioral':
                    return $this->model->getBehavioralRecords($schoolYearId, $sectionId, $gradeLevelId);
                case 'Developmental':
                    return $this->model->getDevelopmentalRecords($schoolYearId, $sectionId, $gradeLevelId);
                case 'Health':
                    return $this->model->getHealthRecords($schoolYearId, $sectionId, $gradeLevelId);
                case 'Attendance':
                    return $this->model->getAttendanceRecords($schoolYearId, $sectionId, $gradeLevelId);
                case 'Achievements':
                    return $this->model->getAchievementRecords($schoolYearId, $sectionId, $gradeLevelId);
                case 'Academic':
                default:
                    return $this->model->getAcademicRecords($schoolYearId, $sectionId, $gradeLevelId);
            }
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
    }

    try{
        $controller = new CompiledRecordsController($con);

        $category = in_array($_GET['category'] ?? '', CompiledRecordsController::CATEGORIES, true) ? $_GET['category'] : 'Academic';
        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== '' ? (int) $_GET['school_year_id'] : null;
        $grade_level_filter = isset($_GET['grade_level_id']) && $_GET['grade_level_id'] !== '' ? (int) $_GET['grade_level_id'] : null;
        $section_filter = isset($_GET['section_id']) && $_GET['section_id'] !== '' ? (int) $_GET['section_id'] : null;

        $compiledRecords = $controller->index($category, $school_year_filter, $section_filter, $grade_level_filter);
        $school_years = $controller->getSchoolYears();
        $grade_levels = $controller->getGradeLevels();
        $sections = $controller->getSections($school_year_filter, $grade_level_filter);
    }catch(Exception $e){
        error_log("Error in CompiledRecordsController: " . $e->getMessage());
        $compiledRecords = [];
        $school_years = [];
        $grade_levels = [];
        $sections = [];
    }
