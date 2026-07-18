<?php
require_once __DIR__ . '/../../core/Model.php';

    class StudentBehavioralProfileModel extends Model{
        protected $behavioral_profiles = 'behavioral_profiles';
        protected $student = 'students';
        protected $sections = 'sections';
        protected $school_year = 'school_year';
        protected $users = 'users';

        public function index($teacher_id, $student_id = null){
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
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->school_year} sy ON bp.school_year_id = sy.id
                    LEFT JOIN {$this->users} u ON bp.recorded_by = u.id
                    WHERE sec.adviser_id = ? AND s.status = 'active'
                ";
                if($student_id !== null){
                    $query .= " AND bp.student_id = ?";
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
                error_log("Error fetching student behavioral records: " . $e->getMessage());
                return [];
            }
        }

        public function getPage($teacher_id, $limit, $offset, $student_id = null){
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
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->school_year} sy ON bp.school_year_id = sy.id
                    LEFT JOIN {$this->users} u ON bp.recorded_by = u.id
                    WHERE sec.adviser_id = ? AND s.status = 'active'
                ";
                if($student_id !== null){
                    $query .= " AND bp.student_id = ?";
                }
                $query .= " ORDER BY bp.observation_date DESC LIMIT ? OFFSET ?";
                $stmt = $this->con->prepare($query);
                if($student_id !== null){
                    $stmt->bind_param("iiii", $teacher_id, $student_id, $limit, $offset);
                }else{
                    $stmt->bind_param("iii", $teacher_id, $limit, $offset);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching student behavioral records page: " . $e->getMessage());
                return [];
            }
        }

        public function countAll($teacher_id, $student_id = null){
            try{
                $query = "SELECT COUNT(*) AS total
                    FROM {$this->behavioral_profiles} bp
                    LEFT JOIN {$this->student} s ON bp.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE sec.adviser_id = ? AND s.status = 'active'
                ";
                if($student_id !== null){
                    $query .= " AND bp.student_id = ?";
                }
                $stmt = $this->con->prepare($query);
                if($student_id !== null){
                    $stmt->bind_param("ii", $teacher_id, $student_id);
                }else{
                    $stmt->bind_param("i", $teacher_id);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting student behavioral records: " . $e->getMessage());
                return 0;
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