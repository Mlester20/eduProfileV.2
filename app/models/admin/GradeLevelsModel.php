<?php
require_once __DIR__ . '/../../core/Model.php';

    class GradeLevelsModel extends Model{
        protected $grade_levels = 'grade_levels';
        
        public function index(){
            try{
                $query = "SELECT * FROM {$this->grade_levels} ORDER BY grade_name ASC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function create($data){
            try{
                $query = "INSERT INTO {$this->grade_levels}(grade_name) VALUES(?)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param(
                    "s",
                    $data['grade_name']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error creating grade level " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->grade_levels} set grade_name = ? WHERE id = ? ";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "si",
                    $data['grade_name'],
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating Grade Level " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->grade_levels} WHERE id = ? ";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting Grade Level " . $e->getMessage());
                return false;
            }
        }
    }