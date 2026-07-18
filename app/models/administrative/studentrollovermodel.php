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
         * (new row, same section/grade level) and archives the original row.
         * @return int|false Number of students rolled over, or false on failure.
         */

        public function rollover(array $studentIds, $newSchoolYearId, $recordedBy){
            $rolledOver = 0;
            $this->con->begin_transaction();
            try{
                $select = $this->con->prepare("SELECT * FROM {$this->students} WHERE id = ? AND status = 'active'");
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
                        $student['grade_level_id'],
                        $student['section_id'],
                        $recordedBy
                    );
                    $insert->execute();

                    $archive->bind_param("i", $studentId);
                    $archive->execute();

                    $rolledOver++;
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
