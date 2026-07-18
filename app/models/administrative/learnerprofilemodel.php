<?php
require_once __DIR__ . '/../../core/Model.php';

    /**
     * One-student, all-categories dossier for the administrative role — the
     * direct answer to the thesis problem statement's "may not be reviewed
     * holistically" complaint. School-wide, no adviser filter (same access
     * model as Compiled Records), and includes archived rows so a learner's
     * full history survives rollover.
     *
     * A learner who has been rolled over exists as multiple `students` rows
     * (one per school year, linked only by matching `lrn` — there is no
     * stable per-learner id in this schema). getOtherYearRecords() surfaces
     * those sibling rows so the admin can jump between years.
     */

    class LearnerProfileModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $users = 'users';
        protected $academic_profiles = 'academic_profiles';
        protected $behavioral_profiles = 'behavioral_profiles';
        protected $developmental_profiles = 'developmental_profiles';
        protected $health_profiles = 'health_profiles';
        protected $attendance = 'attendance';
        protected $achievements_profiles = 'achievements_profiles';

        public function getAllStudentsForSearch(){
            try{
                $query = "SELECT
                    s.id,
                    s.lrn,
                    s.first_name,
                    s.middle_name,
                    s.last_name,
                    s.suffix,
                    s.status,
                    sy.school_year AS school_year,
                    sec.section_name AS section_name,
                    gl.grade_name AS grade_name
                    FROM {$this->students} s
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    ORDER BY s.last_name ASC, s.first_name ASC, sy.id DESC
                ";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching students for learner profile search: " . $e->getMessage());
                return [];
            }
        }

        public function getPage($limit, $offset, $status = null){
            try{
                $query = "SELECT
                    s.id,
                    s.lrn,
                    s.first_name,
                    s.middle_name,
                    s.last_name,
                    s.suffix,
                    s.status,
                    sy.school_year AS school_year,
                    sec.section_name AS section_name,
                    gl.grade_name AS grade_name
                    FROM {$this->students} s
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                ";
                $types = "";
                $params = [];
                if($status !== null){
                    $query .= " WHERE s.status = ?";
                    $types .= "s";
                    $params[] = $status;
                }
                $query .= " ORDER BY s.last_name ASC, s.first_name ASC, sy.id DESC LIMIT ? OFFSET ?";
                $types .= "ii";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching learner master list page: " . $e->getMessage());
                return [];
            }
        }

        public function countAll($status = null){
            try{
                $query = "SELECT COUNT(*) AS total FROM {$this->students} s";
                $types = "";
                $params = [];
                if($status !== null){
                    $query .= " WHERE s.status = ?";
                    $types .= "s";
                    $params[] = $status;
                }
                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting students: " . $e->getMessage());
                return 0;
            }
        }

        public function getStudentInfo($studentId){
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
                    LEFT JOIN {$this->users} ru ON s.recorded_by = ru.id
                    WHERE s.id = ?
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $studentId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching student info: " . $e->getMessage());
                return null;
            }
        }

        public function getOtherYearRecords($lrn, $excludeId){
            try{
                if($lrn === null || $lrn === ''){
                    return [];
                }
                $query = "SELECT
                    s.id,
                    s.status,
                    sy.school_year AS school_year
                    FROM {$this->students} s
                    LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                    WHERE s.lrn = ? AND s.id != ?
                    ORDER BY sy.id DESC
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("si", $lrn, $excludeId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching other-year records: " . $e->getMessage());
                return [];
            }
        }

        private function fetchForStudent($alias, $table, $studentId, $orderBy){
            try{
                $query = "SELECT {$alias}.*, sy.school_year AS school_year, ru.full_name AS recorded_by_name
                    FROM {$table} {$alias}
                    LEFT JOIN {$this->school_year} sy ON {$alias}.school_year_id = sy.id
                    LEFT JOIN {$this->users} ru ON {$alias}.recorded_by = ru.id
                    WHERE {$alias}.student_id = ?
                    ORDER BY {$orderBy}
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $studentId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching {$table} for learner profile: " . $e->getMessage());
                return [];
            }
        }

        public function getAcademicRecords($studentId){
            return $this->fetchForStudent('ap', $this->academic_profiles, $studentId, 'ap.grading_period ASC');
        }

        public function getBehavioralRecords($studentId){
            return $this->fetchForStudent('bp', $this->behavioral_profiles, $studentId, 'bp.observation_date DESC');
        }

        public function getDevelopmentalRecords($studentId){
            return $this->fetchForStudent('dp', $this->developmental_profiles, $studentId, 'dp.domain ASC');
        }

        public function getAttendanceRecords($studentId){
            return $this->fetchForStudent('a', $this->attendance, $studentId, 'a.attendance_date DESC');
        }

        public function getAchievementRecords($studentId){
            return $this->fetchForStudent('ap', $this->achievements_profiles, $studentId, 'ap.date_received DESC');
        }

        public function getHealthProfile($studentId){
            try{
                $query = "SELECT hp.*, sy.school_year AS school_year, ru.full_name AS recorded_by_name
                    FROM {$this->health_profiles} hp
                    LEFT JOIN {$this->school_year} sy ON hp.school_year_id = sy.id
                    LEFT JOIN {$this->users} ru ON hp.recorded_by = ru.id
                    WHERE hp.student_id = ?
                    LIMIT 1
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $studentId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching health profile: " . $e->getMessage());
                return null;
            }
        }
    }
