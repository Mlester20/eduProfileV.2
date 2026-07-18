<?php
require_once __DIR__ . '/../../core/Model.php';

    /**
     * Read-only view of a teacher's own ARCHIVED (rolled-over) students and
     * their historical records — the counterpart to every other teacher
     * model, which filters status = 'active' and so hides these once a
     * student is rolled over. Always scoped to sec.adviser_id = the logged
     * in teacher; never exposes another teacher's students.
     */

    class PastRecordsModel extends Model{
        protected $academic_profiles = 'academic_profiles';
        protected $behavioral_profiles = 'behavioral_profiles';
        protected $developmental_profiles = 'developmental_profiles';
        protected $health_profiles = 'health_profiles';
        protected $attendance = 'attendance';
        protected $achievements_profiles = 'achievements_profiles';
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $users = 'users';

        private function baseJoins($table, $alias){
            return "FROM {$table} {$alias}
                JOIN {$this->students} s ON {$alias}.student_id = s.id
                LEFT JOIN {$this->school_year} sy ON {$alias}.school_year_id = sy.id
                LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                LEFT JOIN {$this->users} ru ON {$alias}.recorded_by = ru.id
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

        private function fetchArchived($alias, $selectPrefix, $table, $teacherId, $schoolYearId, $orderBy, $limit, $offset){
            try{
                $query = "SELECT {$selectPrefix}, " . $this->studentColumns() . " " . $this->baseJoins($table, $alias) . "
                    WHERE sec.adviser_id = ? AND s.status = 'archived'";

                $types = "i";
                $params = [$teacherId];
                if($schoolYearId !== null){
                    $query .= " AND {$alias}.school_year_id = ?";
                    $types .= "i";
                    $params[] = $schoolYearId;
                }
                $query .= " ORDER BY {$orderBy} LIMIT ? OFFSET ?";
                $types .= "ii";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching past {$table} records: " . $e->getMessage());
                return [];
            }
        }

        private function countArchived($alias, $table, $teacherId, $schoolYearId){
            try{
                $query = "SELECT COUNT(*) AS total " . $this->baseJoins($table, $alias) . "
                    WHERE sec.adviser_id = ? AND s.status = 'archived'";

                $types = "i";
                $params = [$teacherId];
                if($schoolYearId !== null){
                    $query .= " AND {$alias}.school_year_id = ?";
                    $types .= "i";
                    $params[] = $schoolYearId;
                }

                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting past {$table} records: " . $e->getMessage());
                return 0;
            }
        }

        public function getAcademicRecords($teacherId, $schoolYearId = null, $limit = 10, $offset = 0){
            return $this->fetchArchived('ap', 'ap.*', $this->academic_profiles, $teacherId, $schoolYearId, 's.last_name ASC, ap.grading_period ASC', $limit, $offset);
        }

        public function getBehavioralRecords($teacherId, $schoolYearId = null, $limit = 10, $offset = 0){
            return $this->fetchArchived('bp', 'bp.*', $this->behavioral_profiles, $teacherId, $schoolYearId, 'bp.observation_date DESC', $limit, $offset);
        }

        public function getDevelopmentalRecords($teacherId, $schoolYearId = null, $limit = 10, $offset = 0){
            return $this->fetchArchived('dp', 'dp.*', $this->developmental_profiles, $teacherId, $schoolYearId, 's.last_name ASC, dp.domain ASC', $limit, $offset);
        }

        public function getHealthRecords($teacherId, $schoolYearId = null, $limit = 10, $offset = 0){
            return $this->fetchArchived('hp', 'hp.*', $this->health_profiles, $teacherId, $schoolYearId, 's.last_name ASC', $limit, $offset);
        }

        public function getAttendanceRecords($teacherId, $schoolYearId = null, $limit = 10, $offset = 0){
            return $this->fetchArchived('a', 'a.*', $this->attendance, $teacherId, $schoolYearId, 'a.attendance_date DESC, s.last_name ASC', $limit, $offset);
        }

        public function getAchievementRecords($teacherId, $schoolYearId = null, $limit = 10, $offset = 0){
            return $this->fetchArchived('ap', 'ap.*', $this->achievements_profiles, $teacherId, $schoolYearId, 'ap.date_received DESC', $limit, $offset);
        }

        public function countRecords($category, $teacherId, $schoolYearId = null){
            switch($category){
                case 'Behavioral':
                    return $this->countArchived('bp', $this->behavioral_profiles, $teacherId, $schoolYearId);
                case 'Developmental':
                    return $this->countArchived('dp', $this->developmental_profiles, $teacherId, $schoolYearId);
                case 'Health':
                    return $this->countArchived('hp', $this->health_profiles, $teacherId, $schoolYearId);
                case 'Attendance':
                    return $this->countArchived('a', $this->attendance, $teacherId, $schoolYearId);
                case 'Achievements':
                    return $this->countArchived('ap', $this->achievements_profiles, $teacherId, $schoolYearId);
                case 'Academic':
                default:
                    return $this->countArchived('ap', $this->academic_profiles, $teacherId, $schoolYearId);
            }
        }

        /**
         * Distinct archived students under this teacher's section(s) — used
         * to populate the School Year filter with only years that actually
         * have past students, rather than every school year ever created.
         */

        public function getArchivedSchoolYears($teacherId){
            try{
                $query = "SELECT DISTINCT sy.id, sy.school_year
                    FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    WHERE sec.adviser_id = ? AND s.status = 'archived'
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
