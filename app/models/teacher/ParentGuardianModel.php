<?php   
require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../../helpers/StudentScope.php';

    class ParentGuardianModel extends Model{
        protected $parent_guardian = 'parents_guardians';
        protected $students = 'students';
        protected $sections = 'sections';
        protected $users = 'users';

        /**
         * Parent/Guardian records for a teacher's own advisees only.
         */

        public function getByTeacher($teacher_id){
            try{
                $query = "SELECT
                    pg.*
                    FROM {$this->parent_guardian} pg
                    LEFT JOIN {$this->students} s ON pg.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE sec.adviser_id = ? AND " . StudentScope::ACTIVE . "
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $teacher_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching parent/guardian records: " . $e->getMessage());
                return [];
            }
        }

        /**
         * The single parent/guardian row for one student, scoped to the
         * given teacher's own advisees. Returns null if none recorded yet.
         */

        public function getByStudent($studentId, $teacherId){
            try{
                $query = "SELECT pg.*
                    FROM {$this->parent_guardian} pg
                    LEFT JOIN {$this->students} s ON pg.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE pg.student_id = ? AND sec.adviser_id = ? AND " . StudentScope::ACTIVE . "
                    LIMIT 1
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $studentId, $teacherId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching parent/guardian for student: " . $e->getMessage());
                return null;
            }
        }

        public function index(){
            try{
                $query = "SELECT
                    pg.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    u.full_name AS recorded_by
                    FROM {$this->parent_guardian} pg
                    LEFT JOIN {$this->students} s ON pg.student_id = s.id
                    LEFT JOIN {$this->users} u ON pg.recorded_by = u.id
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

        /**
         * Scoped to the given teacher's own advisees (matching getByTeacher()),
         * with an optional student_id filter for the Student Information
         * quick-link. Previously unscoped — any teacher could see every
         * other teacher's parent/guardian records.
         */

        public function getPage($limit, $offset, $teacher_id, $studentId = null){
            try{
                $query = "SELECT
                    pg.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    u.full_name AS recorded_by
                    FROM {$this->parent_guardian} pg
                    LEFT JOIN {$this->students} s ON pg.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->users} u ON pg.recorded_by = u.id
                    WHERE sec.adviser_id = ? AND " . StudentScope::ACTIVE . "
                ";
                if($studentId !== null){
                    $query .= " AND pg.student_id = ?";
                }
                $query .= " ORDER BY pg.id DESC LIMIT ? OFFSET ?";
                $stmt = $this->con->prepare($query);
                if($studentId !== null){
                    $stmt->bind_param("iiii", $teacher_id, $studentId, $limit, $offset);
                }else{
                    $stmt->bind_param("iii", $teacher_id, $limit, $offset);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return [];
            }
        }

        public function countAll($teacher_id, $studentId = null){
            try{
                $query = "SELECT COUNT(*) AS total
                    FROM {$this->parent_guardian} pg
                    LEFT JOIN {$this->students} s ON pg.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE sec.adviser_id = ? AND " . StudentScope::ACTIVE . "
                ";
                if($studentId !== null){
                    $query .= " AND pg.student_id = ?";
                }
                $stmt = $this->con->prepare($query);
                if($studentId !== null){
                    $stmt->bind_param("ii", $teacher_id, $studentId);
                }else{
                    $stmt->bind_param("i", $teacher_id);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return 0;
            }
        }

        /**
         * Column => bind-type map, driving both create() and update() so
         * the 35-column insert/update doesn't need to be hand-maintained
         * twice. Each of Father/Mother/Guardian carries the same
         * name/occupation-or-relationship/age/contact + House #/Street/
         * Sitio/Purok/Barangay/City-Municipality/Province breakdown as the
         * student's own address on the `students` table.
         */

        private function fields(){
            return [
                'student_id' => 'i',
                'recorded_by' => 'i',

                'father_name' => 's',
                'father_occupation' => 's',
                'fathers_age' => 'i',
                'father_contact' => 's',
                'father_house_number' => 's',
                'father_street' => 's',
                'father_sitio' => 's',
                'father_purok' => 's',
                'father_barangay' => 's',
                'father_city_municipality' => 's',
                'father_province' => 's',

                'mother_name' => 's',
                'mother_occupation' => 's',
                'mothers_age' => 'i',
                'mother_contact' => 's',
                'mother_house_number' => 's',
                'mother_street' => 's',
                'mother_sitio' => 's',
                'mother_purok' => 's',
                'mother_barangay' => 's',
                'mother_city_municipality' => 's',
                'mother_province' => 's',

                'guardian_name' => 's',
                'guardian_relationship' => 's',
                'guardians_age' => 'i',
                'guardian_contact' => 's',
                'guardian_house_number' => 's',
                'guardian_street' => 's',
                'guardian_sitio' => 's',
                'guardian_purok' => 's',
                'guardian_barangay' => 's',
                'guardian_city_municipality' => 's',
                'guardian_province' => 's',
            ];
        }

        /** Missing/blank values go in as NULL rather than an empty string or a coerced 0. */
        private function paramsFor($columns, $data){
            return array_map(function($col) use ($data){
                $value = $data[$col] ?? null;
                return ($value === '' || $value === null) ? null : $value;
            }, $columns);
        }

        public function create($data){
            try{
                $fields = $this->fields();
                $columns = array_keys($fields);
                $types = implode('', array_values($fields));
                $placeholders = implode(',', array_fill(0, count($columns), '?'));

                $insert = "INSERT INTO {$this->parent_guardian}(" . implode(',', $columns) . ") VALUES ({$placeholders})";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param($types, ...$this->paramsFor($columns, $data));
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                $fields = $this->fields();
                $columns = array_keys($fields);
                $types = implode('', array_values($fields)) . 'i';

                $setClause = implode(', ', array_map(function($col){ return "{$col} = ?"; }, $columns));
                $update = "UPDATE {$this->parent_guardian} SET {$setClause} WHERE id = ?";

                $params = $this->paramsFor($columns, $data);
                $params[] = $id;

                $stmt = $this->con->prepare($update);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->parent_guardian} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }
    }