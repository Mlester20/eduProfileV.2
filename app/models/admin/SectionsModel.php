<?php
require_once __DIR__ . '/../../core/Model.php';

    class SectionsModel extends Model{
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $users = 'users';
        protected $school_year = 'school_year';
        protected $section_teacher_assignments = 'section_teacher_assignments';

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

        /**
         * Sections that actually have students enrolled in the given school
         * year (a section's assignment can change per year via
         * section_teacher_assignments, so this scopes the dropdown to what's
         * relevant for that year rather than every section ever created).
         */

        public function findBySchoolYear($schoolYearId){
            try{
                $query = "SELECT DISTINCT
                    sec.id,
                    sec.section_name,
                    gl.grade_name AS grade_level_name
                    FROM {$this->sections} sec
                    JOIN students s ON s.section_id = sec.id AND s.school_year_id = ?
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    ORDER BY gl.grade_name ASC, sec.section_name ASC
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $schoolYearId);
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

                if(!empty($data['adviser_id'])){
                    $this->recordAssignmentForActiveYear($this->con->insert_id, $data['adviser_id']);
                }

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

                if(!empty($data['adviser_id'])){
                    $this->recordAssignmentForActiveYear($id, $data['adviser_id']);
                }

                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        /**
         * Keeps section_teacher_assignments in sync with sections.adviser_id
         * for the currently active school year, so a later reassignment
         * doesn't silently rewrite who was assigned in past years — anything
         * reading history (e.g. Compiled Records) resolves the teacher via
         * this table first, falling back to the section's current adviser
         * only when no year-specific assignment was ever recorded.
         */

        private function recordAssignmentForActiveYear($sectionId, $teacherId){
            $query = "SELECT id FROM {$this->school_year} WHERE status = 'active' LIMIT 1";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if(!$row){
                return;
            }
            $activeSchoolYearId = (int) $row['id'];

            $upsert = "INSERT INTO {$this->section_teacher_assignments} (section_id, teacher_id, school_year_id) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE teacher_id = VALUES(teacher_id)";
            $stmt = $this->con->prepare($upsert);
            $stmt->bind_param("iii", $sectionId, $teacherId, $activeSchoolYearId);
            $stmt->execute();
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