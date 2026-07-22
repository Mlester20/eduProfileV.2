<?php
require_once __DIR__ . '/../../core/Model.php';

    class AtRiskModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $section_teacher_assignments = 'section_teacher_assignments';
        protected $users = 'users';
        protected $academic_profiles = 'academic_profiles';
        protected $attendance = 'attendance';
        protected $behavioral_profiles = 'behavioral_profiles';

        const FAILING_GRADE = 75;
        const CHRONIC_ABSENCE_THRESHOLD = 5;
        const DISCIPLINARY_THRESHOLD = 3;

        /**
         * One row per active student with three pre-aggregated subquery
         * counts (failing subjects, absences, disciplinary incidents) for
         * the given school year. A student surfaces here if they cross ANY
         * one of the three thresholds. Same section/teacher-resolution
         * joins as CompiledRecordsModel::baseJoins() (section_teacher_assignments
         * first, falling back to sections.adviser_id), but keyed off
         * `students` as the base table since this aggregates across three
         * record tables per student rather than listing individual records.
         */

        private function buildQuery($schoolYearId, $sectionId, $selectExtra = '', $studentId = null){
            $types = "";
            $params = [];

            $failWhere = "grade < " . self::FAILING_GRADE;
            $absnWhere = "status = 'Absent'";
            $discWhere = "category = 'Disciplinary'";

            if($schoolYearId !== null){
                $failWhere .= " AND school_year_id = ?";
                $absnWhere .= " AND school_year_id = ?";
                $discWhere .= " AND school_year_id = ?";
            }

            $query = "SELECT {$selectExtra}
                    s.id AS student_id,
                    s.school_year_id AS school_year_id,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year,
                    sec.section_name AS section_name,
                    gl.grade_name AS grade_name,
                    COALESCE(tu.full_name, adv.full_name) AS assigned_teacher_name,
                    COALESCE(fail.cnt, 0) AS failing_count,
                    COALESCE(absn.cnt, 0) AS absence_count,
                    COALESCE(disc.cnt, 0) AS disciplinary_count
                FROM {$this->students} s
                LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = s.school_year_id
                LEFT JOIN {$this->users} tu ON sta.teacher_id = tu.id
                LEFT JOIN {$this->users} adv ON sec.adviser_id = adv.id
                LEFT JOIN (SELECT student_id, COUNT(*) AS cnt FROM {$this->academic_profiles} WHERE {$failWhere} GROUP BY student_id) fail ON fail.student_id = s.id
                LEFT JOIN (SELECT student_id, COUNT(*) AS cnt FROM {$this->attendance} WHERE {$absnWhere} GROUP BY student_id) absn ON absn.student_id = s.id
                LEFT JOIN (SELECT student_id, COUNT(*) AS cnt FROM {$this->behavioral_profiles} WHERE {$discWhere} GROUP BY student_id) disc ON disc.student_id = s.id
                WHERE s.status = 'active'";

            if($schoolYearId !== null){
                $types .= "iii";
                $params[] = $schoolYearId;
                $params[] = $schoolYearId;
                $params[] = $schoolYearId;
            }

            if($schoolYearId !== null){
                $query .= " AND s.school_year_id = ?";
                $types .= "i";
                $params[] = $schoolYearId;
            }
            if($sectionId !== null){
                $query .= " AND s.section_id = ?";
                $types .= "i";
                $params[] = $sectionId;
            }
            if($studentId !== null){
                $query .= " AND s.id = ?";
                $types .= "i";
                $params[] = $studentId;
            }

            // When looking up a specific known student (getMetricsForStudent),
            // skip the threshold gate — we already know why they're flagged,
            // we just want their numbers for the insight prompt.
            if($studentId === null){
                $query .= " AND (COALESCE(fail.cnt, 0) >= 1
                        OR COALESCE(absn.cnt, 0) >= " . self::CHRONIC_ABSENCE_THRESHOLD . "
                        OR COALESCE(disc.cnt, 0) >= " . self::DISCIPLINARY_THRESHOLD . ")";
            }

            return [$query, $types, $params];
        }

        public function getAtRiskLearners($schoolYearId = null, $sectionId = null){
            try{
                [$query, $types, $params] = $this->buildQuery($schoolYearId, $sectionId);
                $query .= " ORDER BY s.last_name ASC, s.first_name ASC";

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching at-risk learners: " . $e->getMessage());
                return [];
            }
        }

        public function countAtRisk($schoolYearId = null){
            try{
                [$innerQuery, $types, $params] = $this->buildQuery($schoolYearId, null, '');
                $query = "SELECT COUNT(*) AS total FROM ({$innerQuery}) AS at_risk";

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting at-risk learners: " . $e->getMessage());
                return 0;
            }
        }

        public function getMetricsForStudent($studentId, $schoolYearId = null){
            try{
                [$query, $types, $params] = $this->buildQuery($schoolYearId, null, '', $studentId);
                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching at-risk metrics for student: " . $e->getMessage());
                return null;
            }
        }

        public function getInsight($studentId, $schoolYearId){
            try{
                $query = "SELECT insight_text, generated_at FROM at_risk_insights WHERE student_id = ? AND school_year_id = ? LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $studentId, $schoolYearId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching at-risk insight: " . $e->getMessage());
                return null;
            }
        }

        public function saveInsight($studentId, $schoolYearId, $text){
            try{
                $query = "INSERT INTO at_risk_insights (student_id, school_year_id, insight_text) VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE insight_text = VALUES(insight_text), generated_at = CURRENT_TIMESTAMP";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("iis", $studentId, $schoolYearId, $text);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error saving at-risk insight: " . $e->getMessage());
                return false;
            }
        }
    }
