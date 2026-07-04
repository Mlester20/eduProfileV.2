<?php
require_once __DIR__ . '/../../core/Model.php';

    class StudentBehavioralProfileModel extends Model{
        protected $behavioral_profiles = 'behavioral_profiles';
        protected $student = 'students';
        protected $school_year = 'school_year';
        protected $users = 'users';

        public function index(){
            try{
                $query = "SELECT 
                    bp.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year,
                    u.full_name AS recorded_by
                    FROM {$this->behavioral_profiles} bp
                    LEFT JOIN {$this->student} s ON bp.student_id = s.id
                    LEFT JOIN {$this->school_year} sy ON bp.school_year_id = sy.id
                    LEFT JOIN {$this->users} u ON bp.recorded_by = u.id
                ";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching student behavioral records: " . $e->getMessage());
            }
        }

        public function create($data){
            try{
                $insert = "INSERT INTO {$this->behavioral_profiles}(student_id, school_year_id, observation_date, category, observation, intervention, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "iisssssi",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['observation_date'],
                    $data['category'],
                    $data['observation'],
                    $data['intervention'],
                    $data['remarks'],
                    $data['recorded_by']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error creating student behavioral record: " . $e->getMessage());
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->behavioral_profiles} SET student_id = ?, school_year_id = ?, observation_date = ?, category = ?, observation = ?, intervention = ?, remarks = ?, recorded_by = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "iisssssii",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['observation_date'],
                    $data['category'],
                    $data['observation'],
                    $data['intervention'],
                    $data['remarks'],
                    $data['recorded_by'],
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating student behavioral record: " . $e->getMessage());
            }

        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->behavioral_profiles} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting student behavioral record: " . $e->getMessage());
            }
        }
    }