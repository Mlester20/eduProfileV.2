<?php
session_start();
require_once __DIR__ . '/../../models/administrative/duplicatesmodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class DuplicatesController{
        protected $model;
        protected $schoolYearModel;

        public function __construct($con){
            $this->model = new DuplicatesModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
        }

        public function index($schoolYearId = null){
            return $this->model->getPossibleDuplicates($schoolYearId);
        }

        public function getSchoolYears(){
            return $this->schoolYearModel->index();
        }
    }

    try{
        $controller = new DuplicatesController($con);

        $school_years = $controller->getSchoolYears();
        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== '' ? (int) $_GET['school_year_id'] : null;

        $possibleDuplicates = $controller->index($school_year_filter);
    }catch(Exception $e){
        error_log("Error in DuplicatesController: " . $e->getMessage());
        $school_years = [];
        $school_year_filter = null;
        $possibleDuplicates = [];
    }
