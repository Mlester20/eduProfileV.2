<?php
require_once __DIR__ . '/../../core/Model.php';

    class DuplicatesModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $users = 'users';

        /**
         * Flags active students that share the same name + birth date within
         * the same school year — the scenario the problem statement's
         * "duplication of data" names: two teachers each entering the same
         * new learner, undetected because `students.lrn` is optional and its
         * uniqueness constraint doesn't fire on NULL. Archived rows are
         * excluded since a student rolled over into a new year is an
         * intentional new row, not a duplicate.
         *
         * Returns one row per candidate student (not one row per group) so
         * the view can render every match; rows belonging to the same group
         * are adjacent (ordered by the grouping key) and carry a shared
         * dup_count so the view can tell where one group ends and the next
         * begins.
         */

        public function getPossibleDuplicates($schoolYearId = null){
            try{
                $query = "SELECT
                        s.id,
                        s.lrn,
                        s.first_name,
                        s.middle_name,
                        s.last_name,
                        s.suffix,
                        s.birth_date,
                        s.school_year_id,
                        sy.school_year,
                        gl.grade_name,
                        sec.section_name,
                        ru.full_name AS recorded_by_name,
                        dk.dup_count
                    FROM {$this->students} s
                    JOIN (
                        SELECT LOWER(first_name) AS fn, LOWER(last_name) AS ln, birth_date, school_year_id, COUNT(*) AS dup_count
                        FROM {$this->students}
                        WHERE status = 'active'
                        GROUP BY LOWER(first_name), LOWER(last_name), birth_date, school_year_id
                        HAVING COUNT(*) > 1
                    ) dk ON LOWER(s.first_name) = dk.fn AND LOWER(s.last_name) = dk.ln
                        AND s.birth_date = dk.birth_date AND s.school_year_id = dk.school_year_id
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    LEFT JOIN {$this->users} ru ON s.recorded_by = ru.id
                    WHERE s.status = 'active'";

                $types = "";
                $params = [];
                if($schoolYearId !== null){
                    $query .= " AND s.school_year_id = ?";
                    $types .= "i";
                    $params[] = $schoolYearId;
                }
                $query .= " ORDER BY dk.ln ASC, dk.fn ASC, s.birth_date ASC, sy.school_year DESC";

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching possible duplicate students: " . $e->getMessage());
                return [];
            }
        }
    }
