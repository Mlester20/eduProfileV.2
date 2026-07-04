<?php
require_once __DIR__ . '/../../core/Model.php';

    class SectionsModel extends Model{
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $users = 'users'; 

        public function index(){
            try{
                $query = "SELECT 
                    s.*,
                    gl.grade_name AS grade_level_name,
                    u.full_name as assigned_teacher
                    FROM {$this->sections} s
                    LEFT JOIN {$this->grade_levels} gl ON s.grade_level_id = gl.id
                    LEFT JOIN {$this->users} u ON s.adviser_id = u.id
                ";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return[];
            }
        }

        public function findByAdviser($adviserId){
            try{
                $query = "SELECT
                    s.*,
                    gl.grade_name AS grade_level_name,
                    u.full_name as assigned_teacher
                    FROM {$this->sections} s
                    LEFT JOIN {$this->grade_levels} gl ON s.grade_level_id = gl.id
                    LEFT JOIN {$this->users} u ON s.adviser_id = u.id
                    WHERE s.adviser_id = ?
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $adviserId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return[];
            }
        }

        public function create($data){
            try{
                $create = "INSERT INTO {$this->sections} (grade_level_id, section_name, adviser_id) VALUES(?,?,?)";
                $stmt = $this->con->prepare($create);
                $stmt->bind_param(
                    "isi",
                    $data['grade_level_id'],
                    $data['section_name'],
                    $data['adviser_id']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->sections} SET grade_level_id = ?, section_name = ?, adviser_id = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "isii",
                    $data['grade_level_id'],
                    $data['section_name'],
                    $data['adviser_id'],
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->sections} where id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param(
                    "i",
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting section " . $e->getMessage());
                return false;
            }
        }
    }