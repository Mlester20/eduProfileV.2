<?php
require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../../helpers/StudentScope.php';

    /**
     * Per-section "Class List" health/needs report: every active student,
     * not just the flagged subset FeedingProgramModel/AtRiskModel surface,
     * so a teacher can account for every learner (including "Normal, no
     * condition noted" ones) on one roster. health_profiles is LEFT JOINed
     * (not required) so a student with no health profile recorded yet still
     * appears, with blank nutritional status.
     *
     * has_medical_condition is the Health Profile form's actual Yes/No
     * answer (not inferred from text); medical_conditions is only the
     * optional "Specify" detail shown when that answer is Yes. Still not an
     * official PWD/SPED determination on its own — the view labels it as a
     * noted medical condition, not a disability/PWD classification.
     */

    class ClassListModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $section_teacher_assignments = 'latest_section_teacher_assignments';
        protected $users = 'users';
        protected $health_profiles = 'health_profiles';

        private function buildWhere($schoolYearId, $sectionId, $teacherId, $gradeLevelId, $search = null){
            $where = ["" . StudentScope::ACTIVE . ""];
            $types = "";
            $params = [];

            if($schoolYearId !== null){
                $where[] = "s.school_year_id = ?";
                $types .= "i";
                $params[] = $schoolYearId;
            }
            if($sectionId !== null){
                $where[] = "s.section_id = ?";
                $types .= "i";
                $params[] = $sectionId;
            }
            if($teacherId !== null){
                // Same security boundary as AtRiskModel/FeedingProgramModel:
                // a teacher only ever sees their own advisees.
                $where[] = "sec.adviser_id = ?";
                $types .= "i";
                $params[] = $teacherId;
            }
            if($gradeLevelId !== null){
                $where[] = "sec.grade_level_id = ?";
                $types .= "i";
                $params[] = $gradeLevelId;
            }
            if($search !== null && trim($search) !== ''){
                $where[] = "CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name, s.suffix) LIKE ?";
                $types .= "s";
                $params[] = '%' . trim($search) . '%';
            }

            return [" WHERE " . implode(" AND ", $where), $types, $params];
        }

        /**
         * Unpaginated — every matching row, used for Export to Excel / Print
         * (those render the whole filtered list, not just the current page).
         */

        public function getClassList($schoolYearId = null, $sectionId = null, $teacherId = null, $gradeLevelId = null, $search = null){
            try{
                [$whereClause, $types, $params] = $this->buildWhere($schoolYearId, $sectionId, $teacherId, $gradeLevelId, $search);
                $query = $this->baseQuery() . $whereClause . " ORDER BY s.last_name ASC, s.first_name ASC";

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching class list: " . $e->getMessage());
                return [];
            }
        }

        /** Paginated — backs the on-screen table. */

        public function getPage($limit, $offset, $schoolYearId = null, $sectionId = null, $teacherId = null, $gradeLevelId = null, $search = null){
            try{
                [$whereClause, $types, $params] = $this->buildWhere($schoolYearId, $sectionId, $teacherId, $gradeLevelId, $search);
                $query = $this->baseQuery() . $whereClause . " ORDER BY s.last_name ASC, s.first_name ASC LIMIT ? OFFSET ?";
                $types .= "ii";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching class list page: " . $e->getMessage());
                return [];
            }
        }

        public function countAll($schoolYearId = null, $sectionId = null, $teacherId = null, $gradeLevelId = null, $search = null){
            try{
                [$whereClause, $types, $params] = $this->buildWhere($schoolYearId, $sectionId, $teacherId, $gradeLevelId, $search);
                $query = "SELECT COUNT(*) AS total
                    FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    {$whereClause}";

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting class list: " . $e->getMessage());
                return 0;
            }
        }

        private function baseQuery(){
            return "SELECT
                    s.id AS student_id,
                    s.lrn AS lrn,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year,
                    sec.section_name AS section_name,
                    gl.grade_name AS grade_name,
                    COALESCE(tu.full_name, adv.full_name) AS assigned_teacher_name,
                    hp.bmi AS bmi,
                    hp.bmi_classification AS bmi_classification,
                    hp.has_medical_condition AS has_medical_condition,
                    hp.medical_conditions AS medical_conditions,
                    hp.has_special_needs AS has_special_needs,
                    hp.special_needs_details AS special_needs_details
                FROM {$this->students} s
                LEFT JOIN {$this->health_profiles} hp ON hp.student_id = s.id
                LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = s.school_year_id
                LEFT JOIN {$this->users} tu ON sta.teacher_id = tu.id
                LEFT JOIN {$this->users} adv ON sec.adviser_id = adv.id";
        }
    }
