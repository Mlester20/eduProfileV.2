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

        const CATEGORIES = ['Academic', 'Behavioral', 'Developmental', 'Health', 'Attendance', 'Achievements'];

        public function __construct($con){
            $this->model = new PastRecordsModel($con);
        }

        public function index($category, $schoolYearId = null, $page = 1){
            $perPage = 10;
            if(!isset($_SESSION['id'])){
                return array_merge(['data' => []], Paginator::meta(0, $page, $perPage));
            }
            $teacherId = (int) $_SESSION['id'];
            $offset = Paginator::offset($page, $perPage);

            switch($category){
                case 'Behavioral':
                    $rows = $this->model->getBehavioralRecords($teacherId, $schoolYearId, $perPage, $offset);
                    break;
                case 'Developmental':
                    $rows = $this->model->getDevelopmentalRecords($teacherId, $schoolYearId, $perPage, $offset);
                    break;
                case 'Health':
                    $rows = $this->model->getHealthRecords($teacherId, $schoolYearId, $perPage, $offset);
                    break;
                case 'Attendance':
                    $rows = $this->model->getAttendanceRecords($teacherId, $schoolYearId, $perPage, $offset);
                    break;
                case 'Achievements':
                    $rows = $this->model->getAchievementRecords($teacherId, $schoolYearId, $perPage, $offset);
                    break;
                case 'Academic':
                default:
                    $rows = $this->model->getAcademicRecords($teacherId, $schoolYearId, $perPage, $offset);
                    break;
            }

            $total = $this->model->countRecords($category, $teacherId, $schoolYearId);
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
        }

        public function getSchoolYears(){
            if(!isset($_SESSION['id'])){
                return [];
            }
            return $this->model->getArchivedSchoolYears((int) $_SESSION['id']);
        }
    }

    try{
        $controller = new PastRecordsController($con);

        $category = in_array($_GET['category'] ?? '', PastRecordsController::CATEGORIES, true) ? $_GET['category'] : 'Academic';
        $school_year_filter = isset($_GET['school_year_id']) && $_GET['school_year_id'] !== '' ? (int) $_GET['school_year_id'] : null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        $pastRecords = $controller->index($category, $school_year_filter, $page);
        $school_years = $controller->getSchoolYears();
    }catch(Exception $e){
        error_log("Error in PastRecordsController: " . $e->getMessage());
        $pastRecords = ['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'total_pages' => 1];
        $school_years = [];
    }
