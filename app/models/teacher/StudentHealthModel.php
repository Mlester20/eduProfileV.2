<?php
require_once __DIR__ . '/../../core/Model.php';

    class StudentHealthModel extends Model{
        protected $health_profiles = 'health_profiles';
        protected $student = 'students';
        protected $sections = 'sections';
        protected $school_year = 'school_year';

        public function index($teacher_id, $student_id = null){
            try{
                $query = "SELECT
                    hp.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year
                    FROM {$this->health_profiles} hp
                    LEFT JOIN {$this->student} s ON hp.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->school_year} sy ON hp.school_year_id = sy.id
                    WHERE sec.adviser_id = ?
                ";
                if($student_id !== null){
                    $query .= " AND hp.student_id = ?";
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
                error_log("Error fetching student health records: " . $e->getMessage());
                return [];
            }
        }

        public function getById($id, $teacher_id){
            try{
                $query = "SELECT
                    hp.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year
                    FROM {$this->health_profiles} hp
                    LEFT JOIN {$this->student} s ON hp.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->school_year} sy ON hp.school_year_id = sy.id
                    WHERE hp.id = ? AND sec.adviser_id = ?
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $id, $teacher_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching student health record: " . $e->getMessage());
                return null;
            }
        }

        public function create($data){
            try{
                $insert = "INSERT INTO {$this->health_profiles}(student_id, school_year_id, height_cm, weight_kg, bmi, bmi_classification, blood_type, allergies, medical_conditions, vision_screening_result, hearing_screening_result, immunization_status, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "iidddsssssssi",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['height_cm'],
                    $data['weight_kg'],
                    $data['bmi'],
                    $data['bmi_classification'],
                    $data['blood_type'],
                    $data['allergies'],
                    $data['medical_conditions'],
                    $data['vision_screening_result'],
                    $data['hearing_screening_result'],
                    $data['immunization_status'],
                    $data['recorded_by']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error creating student health record: " . $e->getMessage());
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->health_profiles} SET student_id = ?, school_year_id = ?, height_cm = ?, weight_kg = ?, bmi = ?, bmi_classification = ?, blood_type = ?, allergies = ?, medical_conditions = ?, vision_screening_result = ?, hearing_screening_result = ?, immunization_status = ?, recorded_by = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "iidddsssssssii",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['height_cm'],
                    $data['weight_kg'],
                    $data['bmi'],
                    $data['bmi_classification'],
                    $data['blood_type'],
                    $data['allergies'],
                    $data['medical_conditions'],
                    $data['vision_screening_result'],
                    $data['hearing_screening_result'],
                    $data['immunization_status'],
                    $data['recorded_by'],
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating student health record: " . $e->getMessage());
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->health_profiles} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting student health record: " . $e->getMessage());
            }
        }

        public function belongsToTeacher($student_id, $teacher_id){
            try{
                $query = "SELECT s.id FROM {$this->student} s LEFT JOIN {$this->sections} sec ON s.section_id = sec.id WHERE s.id = ? AND sec.adviser_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $student_id, $teacher_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() !== null;
            }catch(Exception $e){
                error_log("Error checking student ownership for health record: " . $e->getMessage());
                return false;
            }
        }

        public function hasHealthProfile($student_id, $excludeId = null){
            try{
                $query = "SELECT id FROM {$this->health_profiles} WHERE student_id = ?";
                if($excludeId !== null){
                    $query .= " AND id != ?";
                }
                $stmt = $this->con->prepare($query);
                if($excludeId !== null){
                    $stmt->bind_param("ii", $student_id, $excludeId);
                }else{
                    $stmt->bind_param("i", $student_id);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() !== null;
            }catch(Exception $e){
                error_log("Error checking existing health profile: " . $e->getMessage());
                return false;
            }
        }

        public function getStudentIdsWithHealthProfile($teacher_id){
            try{
                $query = "SELECT hp.student_id
                    FROM {$this->health_profiles} hp
                    LEFT JOIN {$this->student} s ON hp.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE sec.adviser_id = ?
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $teacher_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return array_column($result->fetch_all(MYSQLI_ASSOC), 'student_id');
            }catch(Exception $e){
                error_log("Error fetching students with health profiles: " . $e->getMessage());
                return [];
            }
        }
    }
