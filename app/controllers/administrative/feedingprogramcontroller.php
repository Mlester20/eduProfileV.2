<?php
session_start();
require_once __DIR__ . '/../../models/administrative/feedingprogrammodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class FeedingProgramController{
        protected $model;
        protected $schoolYearModel;
        protected $sectionsModel;

        public function __construct($con){
            $this->model = new FeedingProgramModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->sectionsModel = new SectionsModel($con);
        }

        public function index($schoolYearId = null, $sectionId = null){
            $learners = $this->model->getFlaggedLearners($schoolYearId, $sectionId);
            foreach($learners as &$learner){
                $cached = $this->model->getInsight($learner['student_id'], $learner['school_year_id']);
                $learner['insight_text'] = $cached['insight_text'] ?? null;
                $learner['insight_generated_at'] = $cached['generated_at'] ?? null;
            }
            unset($learner);
            return $learners;
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }

        public function getActiveSchoolYearId(){
            $activeSyRows = $this->schoolYearModel->getActiveSy();
            return $activeSyRows[0]['id'] ?? null;
        }

        public function getSections($schoolYearId = null){
            if($schoolYearId === null){
                return $this->sectionsModel->index();
            }
            return $this->sectionsModel->findBySchoolYear($schoolYearId);
        }
    }

    try{
        $controller = new FeedingProgramController($con);

        if(isset($_GET['school_year_id']) && $_GET['school_year_id'] !== ''){
            $school_year_filter = (int) $_GET['school_year_id'];
        }else{
            $school_year_filter = $controller->getActiveSchoolYearId();
        }
        $section_filter = isset($_GET['section_id']) && $_GET['section_id'] !== '' ? (int) $_GET['section_id'] : null;

        $feedingProgramLearners = $controller->index($school_year_filter, $section_filter);
        $school_years = $controller->getSchoolYears();
        $sections = $controller->getSections($school_year_filter);
    }catch(Exception $e){
        error_log("Error in FeedingProgramController: " . $e->getMessage());
        $feedingProgramLearners = [];
        $school_years = [];
        $sections = [];
        $school_year_filter = null;
        $section_filter = null;
    }
