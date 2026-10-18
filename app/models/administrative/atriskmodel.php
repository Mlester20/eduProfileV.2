<?php
require_once __DIR__ . '/../../core/Model.php';

    class AtRiskModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $section_teacher_assignments = 'latest_section_teacher_assignments';
        protected $users = 'users';
        protected $academic_profiles = 'academic_profiles';

        const FAILING_GRADE = 75;

        /**
         * One row per active student with a pre-aggregated subquery count
         * of failing subjects for the given school year. A student
         * surfaces here if they have at least one failing subject. Same
         * section/teacher-resolution
         * joins as CompiledRecordsModel::baseJoins() (section_teacher_assignments
         * first, falling back to sections.adviser_id), but keyed off
         * `students` as the base table since this aggregates across three
         * record tables per student rather than listing individual records.
         */

        private function buildQuery($schoolYearId, $sectionId, $selectExtra = '', $teacherId = null, $gradeLevelId = null){
            $types = "";
            $params = [];

            $failWhere = "grade < " . self::FAILING_GRADE;

            if($schoolYearId !== null){
                $failWhere .= " AND school_year_id = ?";
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
                    COALESCE(fail.cnt, 0) AS failing_count
                FROM {$this->students} s
                LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = s.school_year_id
                LEFT JOIN {$this->users} tu ON sta.teacher_id = tu.id
                LEFT JOIN {$this->users} adv ON sec.adviser_id = adv.id
                LEFT JOIN (SELECT student_id, COUNT(*) AS cnt FROM {$this->academic_profiles} WHERE {$failWhere} GROUP BY student_id) fail ON fail.student_id = s.id
                WHERE s.status = 'active'";

            if($schoolYearId !== null){
                $types .= "i";
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
            if($teacherId !== null){
                // Scopes to this teacher's own advisees (the same
                // sections.adviser_id convention every other teacher-side
                // model uses).
                $query .= " AND sec.adviser_id = ?";
                $types .= "i";
                $params[] = $teacherId;
            }
            if($gradeLevelId !== null){
                $query .= " AND sec.grade_level_id = ?";
                $types .= "i";
                $params[] = $gradeLevelId;
            }

            $query .= " AND COALESCE(fail.cnt, 0) >= 1";

            return [$query, $types, $params];
        }

        public function getAtRiskLearners($schoolYearId = null, $sectionId = null, $teacherId = null, $gradeLevelId = null){
            try{
                [$query, $types, $params] = $this->buildQuery($schoolYearId, $sectionId, '', $teacherId, $gradeLevelId);
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

        public function countAtRisk($schoolYearId = null, $teacherId = null){
            try{
                [$innerQuery, $types, $params] = $this->buildQuery($schoolYearId, null, '', $teacherId);
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

    }
