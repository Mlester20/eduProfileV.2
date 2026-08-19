<?php
require_once __DIR__ . '/../../core/Model.php';

    class ReportsModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $section_teacher_assignments = 'section_teacher_assignments';
        protected $users = 'users';
        protected $academic_profiles = 'academic_profiles';
        protected $behavioral_profiles = 'behavioral_profiles';
        protected $developmental_profiles = 'developmental_profiles';
        protected $health_profiles = 'health_profiles';
        protected $attendance = 'attendance';
        protected $achievements_profiles = 'achievements_profiles';
        protected $reading_levels = 'reading_levels';

        /**
         * One row per school year: how many students were enrolled that
         * year and how many records were logged across all seven categories
         * combined, so administrative can see recording activity/trends
         * across years instead of only the currently active one — directly
         * surfaces the "delayed reporting" gap called out in the problem
         * statement (a year with far fewer records than its learner count
         * suggests teachers are behind on entries).
         */

        public function getSchoolYearComparison(){
            try{
                $query = "SELECT
                        sy.id,
                        sy.school_year,
                        sy.status,
                        COUNT(DISTINCT s.id) AS learner_count,
                        (SELECT COUNT(*) FROM {$this->academic_profiles} WHERE school_year_id = sy.id) +
                        (SELECT COUNT(*) FROM {$this->behavioral_profiles} WHERE school_year_id = sy.id) +
                        (SELECT COUNT(*) FROM {$this->developmental_profiles} WHERE school_year_id = sy.id) +
                        (SELECT COUNT(*) FROM {$this->health_profiles} WHERE school_year_id = sy.id) +
                        (SELECT COUNT(*) FROM {$this->attendance} WHERE school_year_id = sy.id) +
                        (SELECT COUNT(*) FROM {$this->achievements_profiles} WHERE school_year_id = sy.id) +
                        (SELECT COUNT(*) FROM {$this->reading_levels} WHERE school_year_id = sy.id) AS total_records
                    FROM {$this->school_year} sy
                    LEFT JOIN {$this->students} s ON s.school_year_id = sy.id
                    GROUP BY sy.id
                    ORDER BY sy.school_year DESC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching school year comparison: " . $e->getMessage());
                return [];
            }
        }

        /**
         * Per-section enrollment snapshot for one school year: active
         * learner count and assigned teacher (section_teacher_assignments
         * first, sections.adviser_id fallback — same resolution as
         * CompiledRecordsModel). Category record counts and at-risk counts
         * are attached by ReportsController per section, reusing
         * CompiledRecordsModel/AtRiskModel rather than duplicating their
         * counting SQL here.
         */

        public function getSectionEnrollment($schoolYearId, $gradeLevelId = null){
            try{
                $query = "SELECT
                        sec.id AS section_id,
                        sec.section_name,
                        gl.grade_name,
                        COALESCE(tu.full_name, adv.full_name) AS teacher_name,
                        COUNT(DISTINCT s.id) AS learner_count
                    FROM {$this->sections} sec
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    LEFT JOIN {$this->students} s ON s.section_id = sec.id AND s.school_year_id = ? AND s.status = 'active'
                    LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = sec.id AND sta.school_year_id = ?
                    LEFT JOIN {$this->users} tu ON sta.teacher_id = tu.id
                    LEFT JOIN {$this->users} adv ON sec.adviser_id = adv.id
                    WHERE 1=1";
                $types = "ii";
                $params = [$schoolYearId, $schoolYearId];
                if($gradeLevelId !== null){
                    $query .= " AND sec.grade_level_id = ?";
                    $types .= "i";
                    $params[] = $gradeLevelId;
                }
                $query .= " GROUP BY sec.id ORDER BY gl.grade_name ASC, sec.section_name ASC";

                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching section enrollment: " . $e->getMessage());
                return [];
            }
        }
    }
