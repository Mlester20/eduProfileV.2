<?php
require_once __DIR__ . '/../../core/Model.php';

    /**
     * Flags active students for the DepEd School-Based Feeding Program
     * (SBFP) based on their recorded BMI classification — the same
     * eligibility criteria DepEd itself uses (Severely Wasted / Wasted on
     * a learner's nutritional status). Structured the same way as
     * AtRiskModel: one query builder shared by the listing, the count,
     * and the single-student lookup used for the AI insight prompt.
     *
     * health_profiles holds one row per student ever (not per school
     * year — see its uniq_student_health_profile key), so a student only
     * surfaces here if their *current* students row has a linked health
     * profile recorded with a target classification.
     */

    class FeedingProgramModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $section_teacher_assignments = 'section_teacher_assignments';
        protected $users = 'users';
        protected $health_profiles = 'health_profiles';

        /** DepEd SBFP eligibility: undernourished classifications only. */
        const TARGET_CLASSIFICATIONS = ['Severely Wasted', 'Wasted'];

        private function buildQuery($schoolYearId, $sectionId, $selectExtra = '', $studentId = null, $teacherId = null){
            $types = "";
            $params = [];

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
                    hp.height_cm AS height_cm,
                    hp.weight_kg AS weight_kg,
                    hp.bmi AS bmi,
                    hp.bmi_classification AS bmi_classification
                FROM {$this->students} s
                JOIN {$this->health_profiles} hp ON hp.student_id = s.id
                LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = s.school_year_id
                LEFT JOIN {$this->users} tu ON sta.teacher_id = tu.id
                LEFT JOIN {$this->users} adv ON sec.adviser_id = adv.id
                WHERE s.status = 'active'";

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
            if($teacherId !== null){
                // Same security boundary as AtRiskModel: a teacher can
                // never fetch/generate an insight for a student who
                // isn't theirs, since the row simply won't match.
                $query .= " AND sec.adviser_id = ?";
                $types .= "i";
                $params[] = $teacherId;
            }

            // Looking up one known student (getMetricsForStudent) skips the
            // classification gate — we already know why they're flagged.
            if($studentId === null){
                $placeholders = implode(',', array_fill(0, count(self::TARGET_CLASSIFICATIONS), '?'));
                $query .= " AND hp.bmi_classification IN ({$placeholders})";
                foreach(self::TARGET_CLASSIFICATIONS as $classification){
                    $types .= "s";
                    $params[] = $classification;
                }
            }

            return [$query, $types, $params];
        }

        public function getFlaggedLearners($schoolYearId = null, $sectionId = null, $teacherId = null){
            try{
                [$query, $types, $params] = $this->buildQuery($schoolYearId, $sectionId, '', null, $teacherId);
                $query .= " ORDER BY s.last_name ASC, s.first_name ASC";

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching feeding program learners: " . $e->getMessage());
                return [];
            }
        }

        public function countFlagged($schoolYearId = null, $teacherId = null){
            try{
                [$innerQuery, $types, $params] = $this->buildQuery($schoolYearId, null, '', null, $teacherId);
                $query = "SELECT COUNT(*) AS total FROM ({$innerQuery}) AS feeding_program";

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting feeding program learners: " . $e->getMessage());
                return 0;
            }
        }

        public function getMetricsForStudent($studentId, $schoolYearId = null, $teacherId = null){
            try{
                [$query, $types, $params] = $this->buildQuery($schoolYearId, null, '', $studentId, $teacherId);
                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching feeding program metrics for student: " . $e->getMessage());
                return null;
            }
        }

        public function getInsight($studentId, $schoolYearId){
            try{
                $query = "SELECT insight_text, generated_at FROM feeding_program_insights WHERE student_id = ? AND school_year_id = ? LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $studentId, $schoolYearId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching feeding program insight: " . $e->getMessage());
                return null;
            }
        }

        public function saveInsight($studentId, $schoolYearId, $text){
            try{
                $query = "INSERT INTO feeding_program_insights (student_id, school_year_id, insight_text) VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE insight_text = VALUES(insight_text), generated_at = CURRENT_TIMESTAMP";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("iis", $studentId, $schoolYearId, $text);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error saving feeding program insight: " . $e->getMessage());
                return false;
            }
        }
    }
