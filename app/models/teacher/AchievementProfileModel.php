<?php
require_once __DIR__ . '/../../core/Model.php';

    class AchievementProfileModel extends Model{
        protected $achievements_profiles = 'achievements_profiles';
        protected $student = 'students';
        protected $sections = 'sections';
        protected $sy = 'school_year';

        public function index($teacher_id, $student_id = null){
            try{
                $query = "SELECT
                    ap.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year
                    FROM {$this->achievements_profiles} ap
                    LEFT JOIN {$this->student} s ON ap.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->sy} sy ON ap.school_year_id = sy.id
                    WHERE sec.adviser_id = ? AND s.status = 'active'
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
                error_log("Error fetching achievement records: " . $e->getMessage());
                return [];
            }
        }

        public function getPage($teacher_id, $limit, $offset, $student_id = null){
            try{
                $query = "SELECT
                    ap.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year
                    FROM {$this->achievements_profiles} ap
                    LEFT JOIN {$this->student} s ON ap.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->sy} sy ON ap.school_year_id = sy.id
                    WHERE sec.adviser_id = ? AND s.status = 'active'
                ";
                if($student_id !== null){
                    $query .= " AND ap.student_id = ?";
                }
                $query .= " ORDER BY ap.date_received DESC LIMIT ? OFFSET ?";
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
                error_log("Error fetching achievement records page: " . $e->getMessage());
                return [];
            }
        }

        public function countAll($teacher_id, $student_id = null){
            try{
                $query = "SELECT COUNT(*) AS total
                    FROM {$this->achievements_profiles} ap
                    LEFT JOIN {$this->student} s ON ap.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE sec.adviser_id = ? AND s.status = 'active'
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
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting achievement records: " . $e->getMessage());
                return 0;
            }
        }

        public function create($data){
            try{
                $insert = "INSERT INTO {$this->achievements_profiles} (student_id, school_year_id, title, category, level, description, date_received, awarding_body, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "iissssssi",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['title'],
                    $data['category'],
                    $data['level'],
                    $data['description'],
                    $data['date_received'],
                    $data['awarding_body'],
                    $data['recorded_by']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error creating achievement record: " . $e->getMessage());
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
                    FROM {$this->achievements_profiles} ap
                    LEFT JOIN {$this->student} s ON ap.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->sy} sy ON ap.school_year_id = sy.id
                    WHERE sec.adviser_id = ? AND s.status = 'active'
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
                error_log("Error fetching achievement record by ID: " . $e->getMessage());
                return null;
            }
        }

        public function update($id, $data){
           try{
                $update = "UPDATE {$this->achievements_profiles} SET
                    student_id = ?,
                    school_year_id = ?,
                    title = ?,
                    category = ?,
                    level = ?,
                    description = ?,
                    date_received = ?,
                    awarding_body = ?,
                    recorded_by = ?
                    WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "iissssssii",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['title'],
                    $data['category'],
                    $data['level'],
                    $data['description'],
                    $data['date_received'],
                    $data['awarding_body'],
                    $data['recorded_by'],
                    $data['id']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating achievement record: " . $e->getMessage());
                return false;
           }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->achievements_profiles} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting achievement record: " . $e->getMessage());
                return false;
            }
        }
    }
