<?php
require_once __DIR__ . '/../../core/Model.php';

    class StudentsModel extends Model{
        protected $students = 'students';
        protected $school_year = 'school_year';
        protected $grade_levels = 'grade_levels';
        protected $sections = 'sections';
        protected $users = 'users';

        public function index(){
            try{
                $query = "SELECT
                    s.*,
                    sy.school_year AS school_year,
                    gl.grade_name AS grade_name,
                    ss.section_name AS section_name,
                    u.full_name AS recorded_by
                    FROM {$this->students} s
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    LEFT JOIN {$this->sections} ss ON s.section_id = ss.id
                    LEFT JOIN {$this->grade_levels} gl ON ss.grade_level_id = gl.id
                    LEFT JOIN {$this->users} u ON s.recorded_by = u.id
                ";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return [];
            }
        }

        public function create($data){
            try{
                $insert = "INSERT INTO {$this->students}(lrn, first_name middle_name, last_name, suffix, birth_date, gender, school_year_id, grade_level_id, section_id, recorded_by) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "sssssssiiii",
                    $data['lrn'],
                    $data['first_name'],
                    $data['middle_name'],
                    $data['last_name'],
                    $data['suffix'],
                    $data['birth_date'],
                    $data['gender'],
                    $data['school_year_id'],
                    $data['grade_level_id'],
                    $data['section_id'],
                    $data['recorded_by'],
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error creating student " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->students} SET first_name = ?, middle_name = ?, last_name = ?, suffix = ?, birth_date = ?, gender = ?, school_year_id = ?, grade_level_id = ?, section_id = ?, recorded_by = ? WHERE id = ? ";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "sssssssiiiii",
                    $data['lrn'],
                    $data['first_name'],
                    $data['middle_name'],
                    $data['last_name'],
                    $data['suffix'],
                    $data['birth_date'],
                    $data['gender'],
                    $data['school_year_id'],
                    $data['grade_level_id'],
                    $data['section_id'],
                    $data['recorded_by'],   
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating student " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->students} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param(
                    "i",
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting student" . $e->getMessage());
                return false;
            }
        }

    }