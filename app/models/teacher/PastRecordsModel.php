<?php
require_once __DIR__ . '/../../core/Model.php';

    /**
     * Read-only view of a teacher's own ARCHIVED (rolled-over) students and
     * their historical records — the counterpart to every other teacher
     * model, which filters status = 'active' and so hides these once a
     * student is rolled over. Scoped to whoever was actually teaching that
     * section in that record's school year (section_teacher_assignments),
     * falling back to sections.adviser_id only when no year-specific
     * assignment was recorded — same pattern as CompiledRecordsModel and
     * AtRiskModel, so a later adviser reassignment can't make a teacher's
     * own archived students vanish (or misattribute to the new adviser).
     *
     * Structured the same way as the administrative LearnerProfileModel —
     * a master list of students, and a per-student getProfile()-style
     * bundle of every category at once — except this one is scoped to a
     * single teacher's own former advisees instead of the whole school.
     */

    class PastRecordsModel extends Model{
        protected $academic_profiles = 'academic_profiles';
        protected $behavioral_profiles = 'behavioral_profiles';
        protected $developmental_profiles = 'developmental_profiles';
        protected $health_profiles = 'health_profiles';
        protected $attendance = 'attendance';
        protected $achievements_profiles = 'achievements_profiles';
        protected $reading_levels = 'reading_levels';
        protected $parents_guardians = 'parents_guardians';
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $section_teacher_assignments = 'section_teacher_assignments';
        protected $users = 'users';

        private function baseJoins($table, $alias){
            return "FROM {$table} {$alias}
                JOIN {$this->students} s ON {$alias}.student_id = s.id
                LEFT JOIN {$this->school_year} sy ON {$alias}.school_year_id = sy.id
                LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                LEFT JOIN {$this->users} ru ON {$alias}.recorded_by = ru.id
                LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = {$alias}.school_year_id
                LEFT JOIN {$this->users} adv ON sec.adviser_id = adv.id
            ";
        }

        private function studentColumns(){
            return "s.first_name AS student_first_name,
                s.middle_name AS student_middle_name,
                s.last_name AS student_last_name,
                s.suffix AS student_suffix,
                sy.school_year AS school_year,
                sec.section_name AS section_name,
                gl.grade_name AS grade_name,
                ru.full_name AS recorded_by_name";
        }

        private function fetchArchived($alias, $selectPrefix, $table, $teacherId, $studentId, $orderBy, $limit = 1000, $offset = 0){
            try{
                $query = "SELECT {$selectPrefix}, " . $this->studentColumns() . " " . $this->baseJoins($table, $alias) . "
                    WHERE COALESCE(sta.teacher_id, sec.adviser_id) = ? AND s.status = 'archived' AND {$alias}.student_id = ?
                    ORDER BY {$orderBy} LIMIT ? OFFSET ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("iiii", $teacherId, $studentId, $limit, $offset);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching past {$table} records: " . $e->getMessage());
                return [];
            }
        }

        public function getAcademicRecords($teacherId, $studentId){
            return $this->fetchArchived('ap', 'ap.*', $this->academic_profiles, $teacherId, $studentId, 'ap.grading_period ASC');
        }

        public function getBehavioralRecords($teacherId, $studentId){
            return $this->fetchArchived('bp', 'bp.*', $this->behavioral_profiles, $teacherId, $studentId, 'bp.observation_date DESC');
        }

        public function getDevelopmentalRecords($teacherId, $studentId){
            return $this->fetchArchived('dp', 'dp.*', $this->developmental_profiles, $teacherId, $studentId, 'dp.domain ASC');
        }

        public function getHealthProfile($teacherId, $studentId){
            $rows = $this->fetchArchived('hp', 'hp.*', $this->health_profiles, $teacherId, $studentId, 'hp.id DESC', 1);
            return $rows[0] ?? null;
        }

        public function getAttendanceRecords($teacherId, $studentId){
            return $this->fetchArchived('a', 'a.*', $this->attendance, $teacherId, $studentId, 'a.attendance_date DESC');
        }

        public function getAchievementRecords($teacherId, $studentId){
            return $this->fetchArchived('ap', 'ap.*', $this->achievements_profiles, $teacherId, $studentId, 'ap.date_received DESC');
        }

        public function getReadingLevelRecords($teacherId, $studentId){
            return $this->fetchArchived('rl', 'rl.*', $this->reading_levels, $teacherId, $studentId, 'rl.assessment_date DESC');
        }

        /**
         * Parent/Guardian record for one archived student. Not teacher-
         * scoped in its own query (parents_guardians has no adviser/status
         * link to check) — safe because getProfile() only calls this after
         * getStudentInfo() already confirmed the student is one of this
         * teacher's own archived advisees.
         */

        public function getParentGuardian($studentId){
            try{
                $query = "SELECT pg.*, ru.full_name AS recorded_by_name
                    FROM {$this->parents_guardians} pg
                    LEFT JOIN {$this->users} ru ON pg.recorded_by = ru.id
                    WHERE pg.student_id = ?
                    LIMIT 1
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $studentId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching parent/guardian for past record: " . $e->getMessage());
                return null;
            }
        }

        /**
         * Single archived student's own info (name, LRN, grade/section at
         * the time they were archived, who recorded them) — teacher-scoped,
         * the anchor row for the consolidated profile view.
         */

        public function getStudentInfo($teacherId, $studentId){
            try{
                $query = "SELECT
                    s.*,
                    sy.school_year AS school_year,
                    sec.section_name AS section_name,
                    gl.grade_name AS grade_name,
                    ru.full_name AS recorded_by_name
                    FROM {$this->students} s
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = s.school_year_id
                    LEFT JOIN {$this->users} ru ON s.recorded_by = ru.id
                    WHERE s.id = ? AND COALESCE(sta.teacher_id, sec.adviser_id) = ? AND s.status = 'archived'
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $studentId, $teacherId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching archived student info: " . $e->getMessage());
                return null;
            }
        }

        /**
         * Paginated master list of this teacher's own archived students —
         * the default landing view, mirroring LearnerProfileModel::getPage()
         * but scoped to one teacher's former advisees instead of the school.
         */

        public function getStudentsPage($teacherId, $limit, $offset, $schoolYearId = null){
            try{
                $query = "SELECT DISTINCT
                    s.id, s.lrn, s.first_name, s.middle_name, s.last_name, s.suffix,
                    sy.school_year AS school_year, sec.section_name AS section_name, gl.grade_name AS grade_name
                    FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = s.school_year_id
                    WHERE COALESCE(sta.teacher_id, sec.adviser_id) = ? AND s.status = 'archived'";
                $types = "i";
                $params = [$teacherId];
                if($schoolYearId !== null){
                    $query .= " AND s.school_year_id = ?";
                    $types .= "i";
                    $params[] = $schoolYearId;
                }
                $query .= " ORDER BY s.last_name ASC, s.first_name ASC LIMIT ? OFFSET ?";
                $types .= "ii";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching archived students page: " . $e->getMessage());
                return [];
            }
        }

        public function countStudents($teacherId, $schoolYearId = null){
            try{
                $query = "SELECT COUNT(DISTINCT s.id) AS total
                    FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = s.school_year_id
                    WHERE COALESCE(sta.teacher_id, sec.adviser_id) = ? AND s.status = 'archived'";
                $types = "i";
                $params = [$teacherId];
                if($schoolYearId !== null){
                    $query .= " AND s.school_year_id = ?";
                    $types .= "i";
                    $params[] = $schoolYearId;
                }
                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting archived students: " . $e->getMessage());
                return 0;
            }
        }

        /**
         * Distinct school years this teacher actually has archived students
         * in — used to populate the School Year filter with only years that
         * have past students, rather than every school year ever created.
         */

        public function getArchivedSchoolYears($teacherId){
            try{
                $query = "SELECT DISTINCT sy.id, sy.school_year
                    FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = s.school_year_id
                    WHERE COALESCE(sta.teacher_id, sec.adviser_id) = ? AND s.status = 'archived'
                    ORDER BY sy.id DESC
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $teacherId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching archived school years: " . $e->getMessage());
                return [];
            }
        }
    }
