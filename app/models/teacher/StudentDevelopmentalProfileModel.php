<?php
require_once __DIR__ . '/../../core/Model.php';

    class StudentDevelopmentalProfileModel extends Model{
        protected $developmental_profiles = 'developmental_profiles';
        protected $students = 'students';
        protected $sections = 'sections';
        protected $users = 'users';
        protected $school_year = 'school_year';

        public function index($teacher_id, $student_id = null){
            try{
                $query = "SELECT
                    dp.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year,
                    u.full_name AS recorded_by
                    FROM {$this->developmental_profiles} dp
                    LEFT JOIN {$this->students} s ON dp.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->school_year} sy ON dp.school_year_id = sy.id
                    LEFT JOIN {$this->users} u ON dp.recorded_by = u.id
                    WHERE sec.adviser_id = ?
                ";
                if($student_id !== null){
                    $query .= " AND dp.student_id = ?";
                }
                $stmt = $this->con->prepare($query);
                if($student_id !== null){
                    $stmt->bind_param("ii", $teacher_id, $student_id);
                }else{
                    $stmt->bind_param("i", $teacher_id);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error while getting data " . $e->getMessage());
                return [];
            }
        }

        public function create($data){
            try{
                $insert = "INSERT INTO {$this->developmental_profiles}(student_id, school_year_id, domain, observation, recommendation, recorded_by) VALUES(?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "iisssi",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['domain'],
                    $data['observation'],
                    $data['recommendation'],
                    $data['recorded_by']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error creating developmental profile " . $e->getMessage());
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->developmental_profiles} SET student_id = ?, school_year_id = ?, domain = ?, observation = ?, recommendation = ?, recorded_by = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "iisssii",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['domain'],
                    $data['observation'],
                    $data['recommendation'],
                    $data['recorded_by'],
                    $id                    
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating developmental profile");
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->developmental_profiles} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting student " . $e->getMessage());
            }
        }
    }