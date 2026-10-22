<?php
require_once __DIR__ . '/../../core/Model.php';

    class SectionsModel extends Model{
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $users = 'users';
        protected $school_year = 'school_year';
        protected $section_teacher_assignments = 'section_teacher_assignments';

        /**
         * Natural pedagogical order (Kinder, then Grade 1..N by number, same
         * rule as GradeLevelsModel::sortKey()) instead of plain alphabetical
         * ordering, which both misorders "Grade 10" before "Grade 2" and —
         * since every section list across the app (filters, rollover,
         * reports, etc.) is built from this same index()/findBySchoolYear()
         * — used to sort by the adviser's last name instead of grade at all.
         */

        private static function sortKey($gradeName, $sectionName = ''){
            $name = trim((string) $gradeName);
            if(stripos($name, 'kinder') !== false){
                $gradeRank = [-1, 0];
            }elseif(preg_match('/(\d+)/', $name, $matches)){
                $gradeRank = [0, (int) $matches[1]];
            }else{
                $gradeRank = [1, 0];
            }
            return [$gradeRank[0], $gradeRank[1], strtolower(trim((string) $sectionName))];
        }

        public function index($gradeLevelId = null){
            try{
                $query = "SELECT
                            s.*,
                            gl.grade_name AS grade_level_name,
                            CONCAT(
                                SUBSTRING_INDEX(u.full_name, ' ', -1),
                                ', ',
                                TRIM(REPLACE(u.full_name, SUBSTRING_INDEX(u.full_name, ' ', -1), ''))
                            ) AS assigned_teacher,
                            (
                                SELECT MAX(sta.created_at)
                                FROM {$this->section_teacher_assignments} sta
                                WHERE sta.section_id = s.id AND sta.teacher_id = s.adviser_id
                            ) AS assigned_since
                        FROM {$this->sections} s
                        LEFT JOIN {$this->grade_levels} gl ON s.grade_level_id = gl.id
                        LEFT JOIN {$this->users} u ON s.adviser_id = u.id";
                $types = "";
                $params = [];
                if($gradeLevelId !== null){
                    $query .= " WHERE s.grade_level_id = ?";
                    $types .= "i";
                    $params[] = $gradeLevelId;
                }
                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                usort($rows, function($a, $b){
                    return self::sortKey($a['grade_level_name'] ?? '', $a['section_name'] ?? '')
                        <=> self::sortKey($b['grade_level_name'] ?? '', $b['section_name'] ?? '');
                });

                return $rows;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return[];
            }
        }

        /**
         * Sections that actually have students enrolled in the given school
         * year (a section's assignment can change per year via
         * section_teacher_assignments, so this scopes the dropdown to what's
         * relevant for that year rather than every section ever created).
         */

        public function findBySchoolYear($schoolYearId, $gradeLevelId = null){
            try{
                $query = "SELECT DISTINCT
                    sec.id,
                    sec.section_name,
                    gl.grade_name AS grade_level_name
                    FROM {$this->sections} sec
                    JOIN students s ON s.section_id = sec.id AND s.school_year_id = ? AND s.deleted_at IS NULL
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    WHERE 1=1
                ";
                $types = "i";
                $params = [$schoolYearId];
                if($gradeLevelId !== null){
                    $query .= " AND sec.grade_level_id = ?";
                    $types .= "i";
                    $params[] = $gradeLevelId;
                }
                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                usort($rows, function($a, $b){
                    return self::sortKey($a['grade_level_name'] ?? '', $a['section_name'] ?? '')
                        <=> self::sortKey($b['grade_level_name'] ?? '', $b['section_name'] ?? '');
                });

                return $rows;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return[];
            }
        }

        public function findById($sectionId){
            try{
                $query = "SELECT
                    sec.id,
                    sec.section_name,
                    gl.grade_name AS grade_level_name
                    FROM {$this->sections} sec
                    LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                    WHERE sec.id = ?
                    LIMIT 1
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $sectionId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error finding section by id: " . $e->getMessage());
                return null;
            }
        }

        /**
         * The section's current adviser name only -- for the "Prepared by"
         * signature line on the Learner's Profile print page. Separate from
         * findById() (which doesn't need the adviser) and from
         * findByAdviser() (which goes the other direction, adviser -> their
         * sections), so neither existing caller is touched.
         */

        public function getAdviserNameBySectionId($sectionId){
            try{
                $query = "SELECT u.full_name
                    FROM {$this->sections} sec
                    LEFT JOIN {$this->users} u ON sec.adviser_id = u.id
                    WHERE sec.id = ?
                    LIMIT 1
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $sectionId);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                return $row['full_name'] ?? null;
            }catch(Exception $e){
                error_log("Error fetching adviser name by section id: " . $e->getMessage());
                return null;
            }
        }

        /**
         * Every recorded teacher assignment across all sections, newest
         * school year first per section — lets admin see who taught a
         * section before the current adviser without needing a dedicated
         * history table (section_teacher_assignments already accumulates
         * one row per section per school year via recordAssignmentForActiveYear()).
         */

        public function getAllAssignmentHistory(){
            try{
                $query = "SELECT
                    sta.section_id,
                    u.full_name AS teacher_name,
                    sy.school_year AS school_year,
                    sy.id AS school_year_id,
                    sta.created_at AS assigned_at
                    FROM {$this->section_teacher_assignments} sta
                    LEFT JOIN {$this->users} u ON sta.teacher_id = u.id
                    LEFT JOIN {$this->school_year} sy ON sta.school_year_id = sy.id
                    ORDER BY sta.section_id ASC, sy.id DESC, sta.id DESC
                ";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching section assignment history: " . $e->getMessage());
                return [];
            }
        }

        /**
         * A teacher can only currently advise one section at a time — this
         * is what create()/update()/updateAdviserOnly() check before writing
         * a new adviser_id, so a teacher already holding another section
         * can't be double-assigned. Excludes the section being edited so
         * re-saving a section without changing its adviser isn't flagged as
         * a conflict with itself.
         */

        public function findSectionByAdviser($teacherId, $excludeSectionId = null){
            try{
                $query = "SELECT s.id, s.section_name, gl.grade_name AS grade_level_name
                    FROM {$this->sections} s
                    LEFT JOIN {$this->grade_levels} gl ON s.grade_level_id = gl.id
                    WHERE s.adviser_id = ?";
                $types = "i";
                $params = [$teacherId];
                if($excludeSectionId !== null){
                    $query .= " AND s.id != ?";
                    $types .= "i";
                    $params[] = $excludeSectionId;
                }
                $query .= " LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                return $stmt->get_result()->fetch_assoc();
            }catch(Exception $e){
                error_log("Error checking existing teacher assignment: " . $e->getMessage());
                return null;
            }
        }

        public function findByAdviser($adviserId){
            try{
                $query = "SELECT
                    s.*,
                    gl.grade_name AS grade_level_name,
                    u.full_name as assigned_teacher
                    FROM {$this->sections} s
                    LEFT JOIN {$this->grade_levels} gl ON s.grade_level_id = gl.id
                    LEFT JOIN {$this->users} u ON s.adviser_id = u.id
                    WHERE s.adviser_id = ?
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $adviserId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return[];
            }
        }

        public function create($data){
            try{
                $create = "INSERT INTO {$this->sections} (grade_level_id, section_name, adviser_id) VALUES(?,?,?)";
                $stmt = $this->con->prepare($create);
                $stmt->bind_param(
                    "isi",
                    $data['grade_level_id'],
                    $data['section_name'],
                    $data['adviser_id']
                );
                $stmt->execute();

                if(!empty($data['adviser_id'])){
                    $this->recordAssignmentForActiveYear($this->con->insert_id, $data['adviser_id']);
                }

                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->sections} SET grade_level_id = ?, section_name = ?, adviser_id = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "isii",
                    $data['grade_level_id'],
                    $data['section_name'],
                    $data['adviser_id'],
                    $id
                );
                $stmt->execute();

                if(!empty($data['adviser_id'])){
                    $this->recordAssignmentForActiveYear($id, $data['adviser_id']);
                }

                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        /**
         * Backs the inline "Assigned Teacher" dropdown in the Grade Levels &
         * Section table — reassigns just the adviser, leaving grade level
         * and section name untouched, so a quick swap doesn't need the full
         * Edit Section form.
         */

        public function updateAdviserOnly($sectionId, $teacherId){
            try{
                $update = "UPDATE {$this->sections} SET adviser_id = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param("ii", $teacherId, $sectionId);
                $stmt->execute();

                $this->recordAssignmentForActiveYear($sectionId, $teacherId);

                return true;
            }catch(Exception $e){
                error_log("Error reassigning section adviser: " . $e->getMessage());
                return false;
            }
        }

        /**
         * Keeps section_teacher_assignments in sync with sections.adviser_id
         * for the currently active school year — appends a new row on every
         * actual reassignment instead of overwriting one in place, so a
         * mid-year replacement (e.g. a teacher leaving) stays recorded
         * instead of silently vanishing. Everything reading "the current
         * teacher" (Reports, Compiled Records, At-Risk, etc.) resolves it
         * via the latest_section_teacher_assignments view, which picks only
         * the newest row per (section_id, school_year_id) — the History
         * modal reads this raw table directly to show the full trail.
         */

        private function recordAssignmentForActiveYear($sectionId, $teacherId){
            $query = "SELECT id FROM {$this->school_year} WHERE status = 'active' LIMIT 1";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if(!$row){
                return;
            }
            $activeSchoolYearId = (int) $row['id'];

            $current = "SELECT teacher_id FROM {$this->section_teacher_assignments}
                WHERE section_id = ? AND school_year_id = ? ORDER BY id DESC LIMIT 1";
            $stmt = $this->con->prepare($current);
            $stmt->bind_param("ii", $sectionId, $activeSchoolYearId);
            $stmt->execute();
            $latest = $stmt->get_result()->fetch_assoc();
            if($latest && (int) $latest['teacher_id'] === (int) $teacherId){
                return;
            }

            $insert = "INSERT INTO {$this->section_teacher_assignments} (section_id, teacher_id, school_year_id) VALUES (?, ?, ?)";
            $stmt = $this->con->prepare($insert);
            $stmt->bind_param("iii", $sectionId, $teacherId, $activeSchoolYearId);
            $stmt->execute();
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->sections} where id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param(
                    "i",
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting section " . $e->getMessage());
                return false;
            }
        }
    }