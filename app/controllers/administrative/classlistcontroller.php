<?php
session_start();
require_once __DIR__ . '/../../models/administrative/classlistmodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    class ClassListController{
        protected $model;
        protected $schoolYearModel;
        protected $sectionsModel;

        public function __construct($con){
            $this->model = new ClassListModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->sectionsModel = new SectionsModel($con);
        }

        public function index($schoolYearId = null, $sectionId = null, $page = 1, $search = null){
            $perPage = 10;
            $offset = Paginator::offset($page, $perPage);
            $rows = $this->model->getPage($perPage, $offset, $schoolYearId, $sectionId, null, null, $search);
            $total = $this->model->countAll($schoolYearId, $sectionId, null, null, $search);
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
        }

        /** Unpaginated — every matching row, for Export to Excel / Print. */

        public function getAll($schoolYearId = null, $sectionId = null, $search = null){
            return $this->model->getClassList($schoolYearId, $sectionId, null, null, $search);
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
        $controller = new ClassListController($con);

        if(isset($_GET['school_year_id']) && $_GET['school_year_id'] !== ''){
            $school_year_filter = (int) $_GET['school_year_id'];
        }else{
            $school_year_filter = $controller->getActiveSchoolYearId();
        }
        $section_filter = isset($_GET['section_id']) && $_GET['section_id'] !== '' ? (int) $_GET['section_id'] : null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $search_filter = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;

        $classListResult = $controller->index($school_year_filter, $section_filter, $page, $search_filter);
        $classList = $classListResult['data'];
        $school_years = $controller->getSchoolYears();
        $sections = $controller->getSections($school_year_filter);
    }catch(Exception $e){
        error_log("Error in administrative ClassListController: " . $e->getMessage());
        $classListResult = ['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'total_pages' => 1];
        $classList = [];
        $school_years = [];
        $sections = [];
        $school_year_filter = null;
        $section_filter = null;
        $search_filter = null;
    }
