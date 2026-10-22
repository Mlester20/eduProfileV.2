<?php
require_once __DIR__ . '/../../core/Model.php';

    class SubjectsModel extends Model{
        protected $subjects = 'subjects';

        public function index(){
            try{
                $query = "SELECT s.*, g.grade_name, u.full_name AS recorded_by_name
                          FROM {$this->subjects} s
                          JOIN grade_levels g ON g.id = s.grade_level_id
                          LEFT JOIN users u ON u.id = s.recorded_by
                          ORDER BY g.id ASC, s.subject_name ASC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function getPage($limit, $offset){
            try{
                $query = "SELECT s.*, g.grade_name, u.full_name AS recorded_by_name
                          FROM {$this->subjects} s
                          JOIN grade_levels g ON g.id = s.grade_level_id
                          LEFT JOIN users u ON u.id = s.recorded_by
                          ORDER BY g.id ASC, s.subject_name ASC
                          LIMIT ? OFFSET ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $limit, $offset);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching subjects page: " . $e->getMessage());
                return [];
            }
        }

        public function countAll(){
            try{
                $result = $this->con->query("SELECT COUNT(*) AS total FROM {$this->subjects}");
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting subjects: " . $e->getMessage());
                return 0;
            }
        }

        /**
         * Subjects for one grade level only, alphabetical -- backs the
         * teacher-side Academic Records "Subject" dropdown, which must
         * only ever offer subjects that actually apply to that specific
         * student's grade level.
         */

        public function getByGradeLevel($gradeLevelId){
            try{
                $query = "SELECT * FROM {$this->subjects} WHERE grade_level_id = ? ORDER BY subject_name ASC";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $gradeLevelId);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching subjects by grade level: " . $e->getMessage());
                return false;
            }
        }

        /**
         * Every subject across a set of grade levels -- a teacher can
         * advise more than one section/grade level, so the "Assign
         * Subjects" checklist needs the union of all of them, grouped by
         * grade.
         */

        public function getByGradeLevels(array $gradeLevelIds){
            if(empty($gradeLevelIds)) return [];
            try{
                $placeholders = implode(',', array_fill(0, count($gradeLevelIds), '?'));
                $query = "SELECT s.*, g.grade_name FROM {$this->subjects} s
                          JOIN grade_levels g ON g.id = s.grade_level_id
                          WHERE s.grade_level_id IN ({$placeholders})
                          ORDER BY g.id ASC, s.subject_name ASC";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param(str_repeat('i', count($gradeLevelIds)), ...$gradeLevelIds);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching subjects by grade levels: " . $e->getMessage());
                return [];
            }
        }

        /**
         * Only the subjects that apply to this specific student's grade
         * level AND are assigned to this specific teacher -- what the
         * teacher's Academic Records "Subject" dropdown actually shows.
         */

        public function getAssignedByGradeLevelAndTeacher($gradeLevelId, $teacherId){
            try{
                $query = "SELECT s.* FROM {$this->subjects} s
                          JOIN subject_teacher_assignments sta ON sta.subject_id = s.id
                          WHERE s.grade_level_id = ? AND sta.teacher_id = ?
                          ORDER BY s.subject_name ASC";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $gradeLevelId, $teacherId);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching assigned subjects: " . $e->getMessage());
                return false;
            }
        }

        public function getAssignedSubjectIds($teacherId){
            try{
                $query = "SELECT subject_id FROM subject_teacher_assignments WHERE teacher_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $teacherId);
                $stmt->execute();
                return array_map('intval', array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'subject_id'));
            }catch(Exception $e){
                error_log("Error fetching assigned subject ids: " . $e->getMessage());
                return [];
            }
        }

        /**
         * Replaces a teacher's full set of subject assignments with
         * exactly `subjectIds` -- simplest correct way to apply an
         * "assign these, unassign everything else" checklist without
         * diffing old vs new by hand.
         */

        public function syncTeacherAssignments($teacherId, array $subjectIds){
            try{
                $this->con->begin_transaction();

                $delete = $this->con->prepare("DELETE FROM subject_teacher_assignments WHERE teacher_id = ?");
                $delete->bind_param("i", $teacherId);
                $delete->execute();

                if(!empty($subjectIds)){
                    $insert = $this->con->prepare("INSERT INTO subject_teacher_assignments (subject_id, teacher_id) VALUES (?, ?)");
                    foreach($subjectIds as $subjectId){
                        $subjectId = (int) $subjectId;
                        $insert->bind_param("ii", $subjectId, $teacherId);
                        $insert->execute();
                    }
                }

                $this->con->commit();
                return true;
            }catch(Exception $e){
                $this->con->rollback();
                error_log("Error syncing teacher subject assignments: " . $e->getMessage());
                return false;
            }
        }

        public function create($data){
            try{
                $query = "INSERT INTO {$this->subjects}(grade_level_id, subject_name, recorded_by) VALUES(?, ?, ?)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param(
                    "isi",
                    $data['grade_level_id'],
                    $data['subject_name'],
                    $data['recorded_by']
                );
                $stmt->execute();
                return $this->con->insert_id;
            }catch(Exception $e){
                error_log("Error creating subject " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->subjects} SET grade_level_id = ?, subject_name = ?, recorded_by = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "isii",
                    $data['grade_level_id'],
                    $data['subject_name'],
                    $data['recorded_by'],
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating subject " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->subjects} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting subject " . $e->getMessage());
                return false;
            }
        }
    }
