<?php
require_once __DIR__ . '/../../core/Model.php';

    class StudentsModel extends Model{
        protected $students = 'students';
        protected $school_year = 'school_year';
        protected $grade_levels = 'grade_levels';
        protected $sections = 'sections';
        protected $users = 'users';

        public function index($teacher_id, $student_id = null){
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
                    WHERE ss.adviser_id = ?
                ";
                if($student_id !== null){
                    $query .= " AND s.id = ?";
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
                error_log("Error " . $e->getMessage());
                return [];
            }
        }

        /**
         * Check if LRN exists to avoid duplicating
         * @return string
         * @return bool
         */

        public function isLrnExists($lrn, $excludeId = null){
            try{
                $query = "SELECT lrn FROM {$this->students} WHERE lrn = ?";
                if($excludeId !== null){
                    $query .= " AND id != ?";
                }
                $stmt = $this->con->prepare($query);
                if($excludeId !== null){
                    $stmt->bind_param('si', $lrn, $excludeId);
                }else{
                    $stmt->bind_param('s', $lrn);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->num_rows > 0;

            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        /**
         * Check if a student belongs to the given teacher's advisory section
         * @return bool
         */

        public function belongsToAdviser($studentId, $teacherId){
            try{
                $query = "SELECT s.id
                    FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE s.id = ? AND sec.adviser_id = ?
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $studentId, $teacherId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->num_rows > 0;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function create($data){
            try{
                $insert = "INSERT INTO {$this->students}(lrn, first_name, middle_name, last_name, suffix, birth_date, gender, address, school_year_id, grade_level_id, section_id, recorded_by) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "ssssssssiiii",
                    $data['lrn'],
                    $data['first_name'],
                    $data['middle_name'],
                    $data['last_name'],
                    $data['suffix'],
                    $data['birth_date'],
                    $data['gender'],
                    $data['address'],
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
                $update = "UPDATE {$this->students} SET lrn = ?, first_name = ?, middle_name = ?, last_name = ?, suffix = ?, birth_date = ?, gender = ?, address = ?, school_year_id = ?, grade_level_id = ?, section_id = ?, recorded_by = ? WHERE id = ? ";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "ssssssssiiiii",
                    $data['lrn'],
                    $data['first_name'],
                    $data['middle_name'],
                    $data['last_name'],
                    $data['suffix'],
                    $data['birth_date'],
                    $data['gender'],
                    $data['address'],
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

        public function getPage(int $limit, int $offset, int $teacher_id): array {
            try {
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
                    WHERE ss.adviser_id = ?
                    LIMIT ? OFFSET ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('iii', $teacher_id, $limit, $offset);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } catch (Exception $e) {
                error_log('Error fetching student page: ' . $e->getMessage());
                return [];
            }
        }

        public function countAll(int $teacher_id): int {
            try {
                $count = 0;
                $query = "SELECT COUNT(*)
                    FROM {$this->students} s
                    LEFT JOIN {$this->sections} ss ON s.section_id = ss.id
                    WHERE ss.adviser_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('i', $teacher_id);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                return (int) $count;
            } catch (Exception $e) {
                error_log('Error counting students: ' . $e->getMessage());
                return 0;
            }
        }

    }