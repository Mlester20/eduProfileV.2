<?php
require_once __DIR__ . '/../../core/Model.php';

    class AcademicProfileModel extends Model{
        protected $academic_profiles = 'academic_profiles';
        protected $student = 'students';
        protected $sections = 'sections';
        protected $sy = 'school_year';
        protected $users = 'users';

        public function index($teacher_id, $student_id = null){
            try{
                $query = "SELECT
                    ap.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year
                    FROM {$this->academic_profiles} ap
                    LEFT JOIN {$this->student} s ON ap.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->sy} sy ON ap.school_year_id = sy.id
                    WHERE sec.adviser_id = ?
                ";
                if($student_id !== null){
                    $query .= " AND ap.student_id = ?";
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
                error_log("Error fetching academic records: " . $e->getMessage());
                return [];
            }
        }

        public function create($data){
            try{
                $insert = "INSERT INTO {$this->academic_profiles} (student_id, school_year_id,subject_name, grading_period, grade, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "iissssi",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['subject_name'],
                    $data['grading_period'], 
                    $data['grade'], 
                    $data['remarks'],
                    $data['recorded_by']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error creating academic record: " . $e->getMessage());
                return false;
            }
        }

        public function getById($teacher_id, $student_id = null){
            try{
                $query = "SELECT
                    ap.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year
                    FROM {$this->academic_profiles} ap
                    LEFT JOIN {$this->student} s ON ap.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->sy} sy ON ap.school_year_id = sy.id
                    WHERE sec.adviser_id = ?
                ";
                if($student_id !== null){
                    $query .= " AND ap.student_id = ?";
                }
                $stmt = $this->con->prepare($query);
                if($student_id !== null){
                    $stmt->bind_param("ii", $teacher_id, $student_id);
                }else{
                    $stmt->bind_param("i", $teacher_id);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching academic record by ID: " . $e->getMessage());
                return null;
            }
        }

        public function update($id, $data){
           try{
                $update = "UPDATE {$this->academic_profiles} SET 
                    student_id = ?, 
                    school_year_id = ?,
                    subject_name = ?, 
                    grading_period = ?, 
                    grade = ?, 
                    remarks = ?, 
                    recorded_by = ?
                    WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "iissssi",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['subject_name'],
                    $data['grading_period'],
                    $data['grade'], 
                    $data['remarks'], 
                    $data['recorded_by'],
                    $data['id']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating academic record: " . $e->getMessage());
                return false;
           }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->academic_profiles} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting academic record: " . $e->getMessage());
                return false;
            }
        }
    }