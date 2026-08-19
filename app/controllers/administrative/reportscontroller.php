<?php
session_start();
require_once __DIR__ . '/../../models/administrative/reportsmodel.php';
require_once __DIR__ . '/../../models/administrative/compiledrecordsmodel.php';
require_once __DIR__ . '/../../models/administrative/atriskmodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class ReportsController{
        protected $model;
        protected $compiledRecordsModel;
        protected $atRiskModel;
        protected $schoolYearModel;
        protected $gradeLevelsModel;

        public function __construct($con){
            $this->model = new ReportsModel($con);
            $this->compiledRecordsModel = new CompiledRecordsModel($con);
            $this->atRiskModel = new AtRiskModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->gradeLevelsModel = new GradeLevelsModel($con);
        }

        public function getSchoolYearComparison(){
            return $this->model->getSchoolYearComparison();
        }

        /**
         * Section enrollment rows plus, per section, record counts for all
         * seven categories and the at-risk count — attached here rather
         * than in ReportsModel so the counting logic stays single-sourced
         * in CompiledRecordsModel/AtRiskModel instead of being duplicated.
         */

        public function getSectionBreakdown($schoolYearId, $gradeLevelId = null){
            $sections = $this->model->getSectionEnrollment($schoolYearId, $gradeLevelId);
            foreach($sections as &$row){
                $sectionId = $row['section_id'];
                $row['academic_count'] = $this->compiledRecordsModel->countAcademicRecords($schoolYearId, $sectionId);
                $row['behavioral_count'] = $this->compiledRecordsModel->countBehavioralRecords($schoolYearId, $sectionId);
                $row['developmental_count'] = $this->compiledRecordsModel->countDevelopmentalRecords($schoolYearId, $sectionId);
                $row['health_count'] = $this->compiledRecordsModel->countHealthRecords($schoolYearId, $sectionId);
                $row['attendance_count'] = $this->compiledRecordsModel->countAttendanceRecords($schoolYearId, $sectionId);
                $row['achievements_count'] = $this->compiledRecordsModel->countAchievementRecords($schoolYearId, $sectionId);
                $row['reading_level_count'] = $this->compiledRecordsModel->countReadingLevelRecords($schoolYearId, $sectionId);
                $row['total_records'] = $row['academic_count'] + $row['behavioral_count'] + $row['developmental_count']
                    + $row['health_count'] + $row['attendance_count'] + $row['achievements_count'] + $row['reading_level_count'];
                $row['at_risk_count'] = count($this->atRiskModel->getAtRiskLearners($schoolYearId, $sectionId));
            }
            unset($row);
            return $sections;
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }

        public function getGradeLevels(){
            return $this->gradeLevelsModel->index();
        }
    }

    try{
        $controller = new ReportsController($con);

        $school_years = $controller->getSchoolYears();
        $grade_levels = $controller->getGradeLevels();

        $activeSy = null;
        foreach($school_years as $sy){
            if($sy['status'] === 'active'){
                $activeSy = $sy;
                break;
            }
        }

        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== ''
            ? (int) $_GET['school_year_id']
            : ($activeSy['id'] ?? null);
        $grade_level_filter = isset($_GET['grade_level_id']) && $_GET['grade_level_id'] !== '' ? (int) $_GET['grade_level_id'] : null;

        $schoolYearComparison = $controller->getSchoolYearComparison();
        $sectionBreakdown = $school_year_filter !== null
            ? $controller->getSectionBreakdown($school_year_filter, $grade_level_filter)
            : [];
    }catch(Exception $e){
        error_log("Error in ReportsController: " . $e->getMessage());
        $school_years = [];
        $grade_levels = [];
        $school_year_filter = null;
        $grade_level_filter = null;
        $schoolYearComparison = [];
        $sectionBreakdown = [];
    }
