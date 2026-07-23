<?php
require_once __DIR__ . '/../../core/Model.php';

    class StudentRolloverModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';

        public function getActiveStudentsBySchoolYear($schoolYearId){
            try{
                $query = "SELECT
                    s.*,
                    gl.grade_name AS grade_name,
                    sec.section_name AS section_name
                    FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    WHERE s.school_year_id = ? AND s.status = 'active'
                    ORDER BY s.last_name ASC, s.first_name ASC
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $schoolYearId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching students for rollover: " . $e->getMessage());
                return [];
            }
        }

        /**
         * Copies each selected student forward into the target school year
         * (new row) and archives the original row. When $targetSections has
         * an entry for a student (keyed by student id, valued by the target
         * section id), the new row is promoted into that section/grade level
         * instead of staying in the old one — without this, a rolled-over
         * student would remain labeled under their old grade/section/teacher
         * forever, defeating the point of archiving. Falls back to copying
         * the old grade_level_id/section_id when no target is given, so
         * rollover without an explicit promotion still works as before.
         * @return int|false Number of students rolled over, or false on failure.
         */

        public function rollover(array $studentIds, $newSchoolYearId, $recordedBy, array $targetSections = []){
            $rolledOver = 0;
            $this->con->begin_transaction();
            try{
                $select = $this->con->prepare("SELECT * FROM {$this->students} WHERE id = ? AND status = 'active'");
                $sectionLookup = $this->con->prepare("SELECT grade_level_id FROM {$this->sections} WHERE id = ?");
                $insert = $this->con->prepare(
                    "INSERT INTO {$this->students} (lrn, first_name, middle_name, last_name, suffix, birth_date, gender, address, school_year_id, grade_level_id, section_id, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $archive = $this->con->prepare("UPDATE {$this->students} SET status = 'archived' WHERE id = ?");

                foreach($studentIds as $studentId){
                    $studentId = (int) $studentId;

                    $select->bind_param("i", $studentId);
                    $select->execute();
                    $student = $select->get_result()->fetch_assoc();
                    if(!$student){
                        continue;
                    }

                    $gradeLevelId = $student['grade_level_id'];
                    $sectionId = $student['section_id'];

                    if(!empty($targetSections[$studentId])){
                        $targetSectionId = (int) $targetSections[$studentId];
                        $sectionLookup->bind_param("i", $targetSectionId);
                        $sectionLookup->execute();
                        $targetSection = $sectionLookup->get_result()->fetch_assoc();
                        if($targetSection){
                            $gradeLevelId = $targetSection['grade_level_id'];
                            $sectionId = $targetSectionId;
                        }
                    }

                    $insert->bind_param(
                        "ssssssssiiii",
                        $student['lrn'],
                        $student['first_name'],
                        $student['middle_name'],
                        $student['last_name'],
                        $student['suffix'],
                        $student['birth_date'],
                        $student['gender'],
                        $student['address'],
                        $newSchoolYearId,
                        $gradeLevelId,
                        $sectionId,
                        $recordedBy
                    );
                    $insert->execute();

                    $archive->bind_param("i", $studentId);
                    $archive->execute();

                    $rolledOver++;
                }

                if($rolledOver > 0){
                    // Teacher-facing dashboards/filters (e.g. DashboardController::getStats())
                    // default to whichever school year is flagged 'active', so without this
                    // a rollover's target year silently stays invisible until someone
                    // manually flips it in Manage School Year — even though the students
                    // themselves were already promoted correctly.
                    $deactivate = $this->con->prepare("UPDATE school_year SET status = 'inactive' WHERE status = 'active' AND id != ?");
                    $deactivate->bind_param("i", $newSchoolYearId);
                    $deactivate->execute();

                    $activate = $this->con->prepare("UPDATE school_year SET status = 'active' WHERE id = ?");
                    $activate->bind_param("i", $newSchoolYearId);
                    $activate->execute();
                }

                $this->con->commit();
                return $rolledOver;
            }catch(Exception $e){
                $this->con->rollback();
                error_log("Error rolling over students: " . $e->getMessage());
                return false;
            }
        }
    }
