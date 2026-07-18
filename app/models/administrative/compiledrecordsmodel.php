<?php
require_once __DIR__ . '/../../core/Model.php';

    class CompiledRecordsModel extends Model{
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
        protected $section_teacher_assignments = 'section_teacher_assignments';
        protected $users = 'users';

        /**
         * Shared FROM/JOIN scaffold every category query builds on: resolves
         * the student's section/grade, the school year the record belongs to,
         * who recorded it, and which teacher was assigned to that section for
         * that school year — regardless of who the section's adviser is
         * today. No adviser/teacher_id filter is ever applied here;
         * administrative compiles across every teacher.
         *
         * section_teacher_assignments is the per-year assignment history, but
         * nothing in this codebase currently writes to it, so it's empty in
         * practice. sections.adviser_id is the table every other module
         * actually uses to know who teaches a section, so it's the fallback
         * whenever there's no matching assignment row for that year.
         */

        private function baseJoins($table, $alias){
            return "FROM {$table} {$alias}
                JOIN {$this->students} s ON {$alias}.student_id = s.id
                LEFT JOIN {$this->school_year} sy ON {$alias}.school_year_id = sy.id
                LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                LEFT JOIN {$this->users} ru ON {$alias}.recorded_by = ru.id
                LEFT JOIN {$this->section_teacher_assignments} sta ON sta.section_id = s.section_id AND sta.school_year_id = {$alias}.school_year_id
                LEFT JOIN {$this->users} tu ON sta.teacher_id = tu.id
                LEFT JOIN {$this->users} adv ON sec.adviser_id = adv.id
            ";
        }

        private function studentColumns(){
            return "s.first_name AS student_first_name,
                s.middle_name AS student_middle_name,
                s.last_name AS student_last_name,
                s.suffix AS student_suffix,
                s.status AS student_status,
                sy.school_year AS school_year,
                sec.section_name AS section_name,
                gl.grade_name AS grade_name,
                ru.full_name AS recorded_by_name,
                COALESCE(tu.full_name, adv.full_name) AS assigned_teacher_name";
        }

        private function fetchFiltered($alias, $selectPrefix, $table, $schoolYearId, $sectionId, $orderBy){
            try{
                $query = "SELECT {$selectPrefix}, " . $this->studentColumns() . " " . $this->baseJoins($table, $alias) . " WHERE 1=1";

                $types = "";
                $params = [];
                if($schoolYearId !== null){
                    $query .= " AND {$alias}.school_year_id = ?";
                    $types .= "i";
                    $params[] = $schoolYearId;
                }
                if($sectionId !== null){
                    $query .= " AND s.section_id = ?";
                    $types .= "i";
                    $params[] = $sectionId;
                }
                $query .= " ORDER BY {$orderBy}";

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching compiled {$table} records: " . $e->getMessage());
                return [];
            }
        }

        private function countFiltered($alias, $table, $schoolYearId, $sectionId){
            try{
                $query = "SELECT COUNT(*) AS total FROM {$table} {$alias}
                    JOIN {$this->students} s ON {$alias}.student_id = s.id
                    WHERE 1=1";

                $types = "";
                $params = [];
                if($schoolYearId !== null){
                    $query .= " AND {$alias}.school_year_id = ?";
                    $types .= "i";
                    $params[] = $schoolYearId;
                }
                if($sectionId !== null){
                    $query .= " AND s.section_id = ?";
                    $types .= "i";
                    $params[] = $sectionId;
                }

                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return (int) ($result->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting compiled {$table} records: " . $e->getMessage());
                return 0;
            }
        }

        public function countAcademicRecords($schoolYearId = null, $sectionId = null){
            return $this->countFiltered('ap', $this->academic_profiles, $schoolYearId, $sectionId);
        }

        public function countBehavioralRecords($schoolYearId = null, $sectionId = null){
            return $this->countFiltered('bp', $this->behavioral_profiles, $schoolYearId, $sectionId);
        }

        public function countDevelopmentalRecords($schoolYearId = null, $sectionId = null){
            return $this->countFiltered('dp', $this->developmental_profiles, $schoolYearId, $sectionId);
        }

        public function countHealthRecords($schoolYearId = null, $sectionId = null){
            return $this->countFiltered('hp', $this->health_profiles, $schoolYearId, $sectionId);
        }

        public function countAttendanceRecords($schoolYearId = null, $sectionId = null){
            return $this->countFiltered('a', $this->attendance, $schoolYearId, $sectionId);
        }

        public function countAchievementRecords($schoolYearId = null, $sectionId = null){
            return $this->countFiltered('ap', $this->achievements_profiles, $schoolYearId, $sectionId);
        }

        public function getAcademicRecords($schoolYearId = null, $sectionId = null){
            return $this->fetchFiltered('ap', 'ap.*', $this->academic_profiles, $schoolYearId, $sectionId, 's.last_name ASC, ap.grading_period ASC');
        }

        public function getBehavioralRecords($schoolYearId = null, $sectionId = null){
            return $this->fetchFiltered('bp', 'bp.*', $this->behavioral_profiles, $schoolYearId, $sectionId, 'bp.observation_date DESC');
        }

        public function getDevelopmentalRecords($schoolYearId = null, $sectionId = null){
            return $this->fetchFiltered('dp', 'dp.*', $this->developmental_profiles, $schoolYearId, $sectionId, 's.last_name ASC, dp.domain ASC');
        }

        public function getHealthRecords($schoolYearId = null, $sectionId = null){
            return $this->fetchFiltered('hp', 'hp.*', $this->health_profiles, $schoolYearId, $sectionId, 's.last_name ASC');
        }

        public function getAttendanceRecords($schoolYearId = null, $sectionId = null){
            return $this->fetchFiltered('a', 'a.*', $this->attendance, $schoolYearId, $sectionId, 'a.attendance_date DESC, s.last_name ASC');
        }

        public function getAchievementRecords($schoolYearId = null, $sectionId = null){
            return $this->fetchFiltered('ap', 'ap.*', $this->achievements_profiles, $schoolYearId, $sectionId, 'ap.date_received DESC');
        }
    }
