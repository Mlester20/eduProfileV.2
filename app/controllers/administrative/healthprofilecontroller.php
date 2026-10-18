<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
require_once __DIR__ . '/../../models/administrative/compiledrecordsmodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    /**
     * Read-only, school-wide Health Profile listing — no adviser/teacher
     * filter, same access model as Compiled Records and Reports. Reuses
     * CompiledRecordsModel::getHealthRecords() rather than introducing a
     * new query, since that's the same data Reports/Compiled Records
     * already aggregate from.
     */

    class HealthProfileController{
        protected $compiledRecordsModel;
        protected $schoolYearModel;
        protected $sectionsModel;
        protected $gradeLevelsModel;

        public function __construct($con){
            $this->compiledRecordsModel = new CompiledRecordsModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->sectionsModel = new SectionsModel($con);
            $this->gradeLevelsModel = new GradeLevelsModel($con);
        }

        public function index($schoolYearId = null, $sectionId = null, $gradeLevelId = null){
            return $this->compiledRecordsModel->getHealthRecords($schoolYearId, $sectionId, $gradeLevelId);
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }

        public function getActiveSchoolYearId(){
            $activeSyRows = $this->schoolYearModel->getActiveSy();
            return $activeSyRows[0]['id'] ?? null;
        }

        public function getSections(){
            return $this->sectionsModel->index();
        }

        public function getGradeLevels(){
            return $this->gradeLevelsModel->index();
        }
    }

    try{
        $controller = new HealthProfileController($con);

        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== ''
            ? (int) $_GET['school_year_id']
            : $controller->getActiveSchoolYearId();
        $section_filter = isset($_GET['section_id']) && $_GET['section_id'] !== '' ? (int) $_GET['section_id'] : null;
        $grade_level_filter = isset($_GET['grade_level_id']) && $_GET['grade_level_id'] !== '' ? (int) $_GET['grade_level_id'] : null;

        $healthRecords = $controller->index($school_year_filter, $section_filter, $grade_level_filter);
        $school_years = $controller->getSchoolYears();
        $sections = $controller->getSections();
        $grade_levels = $controller->getGradeLevels();
    }catch(Exception $e){
        error_log("Error in HealthProfileController: " . $e->getMessage());
        $healthRecords = [];
        $school_years = [];
        $sections = [];
        $grade_levels = [];
        $school_year_filter = null;
        $section_filter = null;
        $grade_level_filter = null;
    }
