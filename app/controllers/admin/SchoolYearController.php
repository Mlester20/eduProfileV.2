<?php
session_start();

require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../middleware/Auth.php';

    class SchoolYearController extends Controller{

        public function __construct($con){
            parent::__construct(new SchoolYearModel($con));
        }

        public function index(){
            try{
                return $this->model->index();
            }catch(Exception $e){
                return $e->getMessage();
            }
        }

        public function create($data){
            if(empty($data['school_year']) || empty($data['start_date']) || empty($data['end_date'])){
                FlashMessage::setFlash('error', 'All fields are required.');
                header('Location: ../../../resources/views/admin/sy.php');
                exit();
            }else{
                if($this->model->create($data)){
                    FlashMessage::setFlash('success', 'School year created successfully.');
                    header('Location: ../../../resources/views/admin/sy.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to create school year.');
                    header('Location: ../../../resources/views/admin/sy.php');
                    exit();
                }
            }
        }

        //function to avoid deleting active school year
        public function canDelete($id){
            $school_years = $this->model->index();
            foreach($school_years as $sy){
                if($sy['id'] == $id && $sy['status'] == 'active'){
                    return false;
                }
            }
            return true;
        }

        public function update($id, $data){
            if(empty($data['school_year']) || empty($data['start_date']) || empty($data['end_date']) || empty($data['status'])){
                FlashMessage::setFlash('error', 'All fields are required.');
                header('Location: ../../../resources/views/admin/sy.php');
                exit();
            }else{
                try{
                    if($this->model->update($id, $data)){
                        FlashMessage::setFlash('success', 'School year updated successfully.');
                        header('Location: ../../../resources/views/admin/sy.php');
                        exit();
                    }else{
                        FlashMessage::setFlash('error', 'Failed to update school year.');
                        header('Location: ../../../resources/views/admin/sy.php');
                        exit();
                    }
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }
        }

        public function delete($id){
            try{
                // Check if the school year can be deleted
                if(!$this->canDelete($id)){
                    FlashMessage::setFlash('error', 'This is an active school year and cannot be deleted.');
                    header('Location: ../../../resources/views/admin/sy.php');
                    exit();
                }
                if($this->model->delete(['id' => $id])){
                    FlashMessage::setFlash('success', 'School year deleted successfully.');
                    header('Location: ../../../resources/views/admin/sy.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to delete school year.');
                    header('Location: ../../../resources/views/admin/sy.php');
                    exit();
                }
            }catch(Exception $e){
                return $e->getMessage();
            }
        }

    }

    try{
        $controller = new SchoolYearController($con);
        $school_years = $controller->index();
    }catch(Exception $e){
        return $e->getMessage();
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST['create_sy'])){
            $controller->create([
                'school_year' => $_POST['school_year'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date']
            ]);
        }if(isset($_POST['update_sy'])){
            $sy_id = $_POST['id'] ?? null;
            $controller->update(
                $sy_id,
                [
                    'school_year' => $_POST['school_year'],
                    'start_date' => $_POST['start_date'],
                    'end_date' => $_POST['end_date'],
                    'status' => $_POST['status']
                ]
            );
        }if(isset($_POST['delete_sy'])){
            $controller->delete($_POST['id']);
        }
    }