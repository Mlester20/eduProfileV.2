<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../../database/config/config.php';

    class StudentsController extends Controller{
        protected $auditLogs;
        protected $sy;
        protected $grade_level;
        protected $section;

        public function __construct($con){
            parent::__construct(
                new StudentsModel($con)
            );
            $this->auditLogs = new AuditLogs($con);
            $this->sy = new SchoolYearModel($con);
            $this->grade_level = new GradeLevelsModel($con);
            $this->section = new SectionsModel($con);
        }

        public function index(){
            return $this->model->index();
        }

        /**
         * Get only active school year
         * @return string
         */

        public function activeSy(){
            return $this->sy->getActiveSy();
        }

        public function create($data){

        }

        public function update($id, $data){

        }

        public function delete($id){

        }
    }

    try{
        $controller = new StudentsController($con);
        $students = $controller->index();
    }catch(Exception $e){
        throw new Exception("Error " . $e->getMessage());
    }