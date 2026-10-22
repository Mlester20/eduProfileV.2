<?php
require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../../helpers/StudentScope.php';

    /**
     * Trashed students (students.deleted_at IS NOT NULL). Ownership follows
     * the same rule as every other teacher-facing student query: the
     * student's section's adviser_id, not who happened to click Delete --
     * so if a section is ever reassigned, the new adviser sees that
     * section's trash too, consistent with how Students/Academic
     * Records/etc. already work. $teacherId = null lists every teacher's
     * trash (used by the administrative-side Trash page).
     */

    class TrashModel extends Model{
        protected $students = 'students';
        protected $sections = 'sections';
        protected $grade_levels = 'grade_levels';
        protected $school_year = 'school_year';
        protected $users = 'users';

        /**
         * days_left is computed entirely in MySQL (DATEDIFF against NOW())
         * rather than PHP's time()/strtotime() against a MySQL-written
         * timestamp -- this app's PHP timezone (Europe/Berlin) runs hours
         * behind the MySQL server clock, and mixing the two for date math
         * silently produces a wrong "days left" (already bit this app
         * once before, in the Forgot Password OTP feature).
         */
        private function baseSelect($retentionDays){
            return "SELECT
                s.id, s.lrn, s.first_name, s.middle_name, s.last_name, s.suffix, s.picture,
                s.deleted_at, s.deleted_by,
                GREATEST(0, {$retentionDays} - DATEDIFF(NOW(), s.deleted_at)) AS days_left,
                db.full_name AS deleted_by_name,
                adv.full_name AS adviser_name,
                sy.school_year AS school_year,
                gl.grade_name AS grade_name,
                sec.section_name AS section_name
                FROM {$this->students} s
                LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                LEFT JOIN {$this->grade_levels} gl ON sec.grade_level_id = gl.id
                LEFT JOIN {$this->school_year} sy ON s.school_year_id = sy.id
                LEFT JOIN {$this->users} db ON s.deleted_by = db.id
                LEFT JOIN {$this->users} adv ON sec.adviser_id = adv.id";
        }

        public function getPage($limit, $offset, $teacherId = null, ?string $search = null){
            try{
                $retentionDays = (int) StudentScope::retentionDays();
                $query = $this->baseSelect($retentionDays) . " WHERE " . StudentScope::TRASHED;
                $types = "";
                $params = [];
                if($teacherId !== null){
                    $query .= " AND sec.adviser_id = ?";
                    $types .= "i";
                    $params[] = $teacherId;
                }
                if($search !== null && $search !== ''){
                    $query .= " AND (s.lrn LIKE ? OR s.first_name LIKE ? OR s.middle_name LIKE ? OR s.last_name LIKE ?)";
                    $like = '%' . $search . '%';
                    $types .= "ssss";
                    array_push($params, $like, $like, $like, $like);
                }
                $query .= " ORDER BY s.deleted_at DESC LIMIT ? OFFSET ?";
                $types .= "ii";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching trash page: " . $e->getMessage());
                return [];
            }
        }

        public function countAll($teacherId = null, ?string $search = null){
            try{
                $query = "SELECT COUNT(*) AS total FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE " . StudentScope::TRASHED;
                $types = "";
                $params = [];
                if($teacherId !== null){
                    $query .= " AND sec.adviser_id = ?";
                    $types .= "i";
                    $params[] = $teacherId;
                }
                if($search !== null && $search !== ''){
                    $query .= " AND (s.lrn LIKE ? OR s.first_name LIKE ? OR s.middle_name LIKE ? OR s.last_name LIKE ?)";
                    $like = '%' . $search . '%';
                    $types .= "ssss";
                    array_push($params, $like, $like, $like, $like);
                }
                $stmt = $this->con->prepare($query);
                if($types !== ""){
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                return (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
            }catch(Exception $e){
                error_log("Error counting trash: " . $e->getMessage());
                return 0;
            }
        }

        /** Badge count for the sidebar. */
        public function countForTeacher($teacherId){
            return $this->countAll($teacherId);
        }

        /**
         * Administrative-side check: is this row trashed at all, regardless
         * of which teacher's section it belongs to? Administrative staff
         * oversee every teacher's trash, so there's no single owner to
         * scope against the way belongsToOwner() does for a teacher.
         */
        public function existsTrashed($id){
            try{
                $query = "SELECT s.id FROM {$this->students} s WHERE s.id = ? AND " . StudentScope::TRASHED;
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return $stmt->get_result()->num_rows > 0;
            }catch(Exception $e){
                error_log("Error checking trashed existence: " . $e->getMessage());
                return false;
            }
        }

        /** All trashed ids school-wide -- for administrative's Empty Trash. */
        public function getAllTrashedIds(){
            try{
                $query = "SELECT s.id FROM {$this->students} s WHERE " . StudentScope::TRASHED;
                $result = $this->con->query($query);
                return array_column($result->fetch_all(MYSQLI_ASSOC), 'id');
            }catch(Exception $e){
                error_log("Error listing all trashed ids: " . $e->getMessage());
                return [];
            }
        }

        /**
         * Ownership check for a trashed row -- belongsToAdviser() in
         * StudentsModel requires status='active' so it never matches a
         * trashed row; this is the Trash-specific equivalent.
         */
        public function belongsToOwner($id, $teacherId){
            try{
                $query = "SELECT s.id FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE s.id = ? AND sec.adviser_id = ? AND " . StudentScope::TRASHED;
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $id, $teacherId);
                $stmt->execute();
                return $stmt->get_result()->num_rows > 0;
            }catch(Exception $e){
                error_log("Error checking trash ownership: " . $e->getMessage());
                return false;
            }
        }

        public function getPicturePath($id){
            try{
                $stmt = $this->con->prepare("SELECT picture FROM {$this->students} WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                return $row['picture'] ?? null;
            }catch(Exception $e){
                error_log("Error fetching picture path: " . $e->getMessage());
                return null;
            }
        }

        public function getInfo($id){
            try{
                $stmt = $this->con->prepare("SELECT lrn, first_name, middle_name, last_name, suffix, school_year_id FROM {$this->students} WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return $stmt->get_result()->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching trashed student info: " . $e->getMessage());
                return null;
            }
        }

        /** All trashed ids a given teacher owns -- for Empty Trash. */
        public function getTrashedIdsForTeacher($teacherId){
            try{
                $query = "SELECT s.id FROM {$this->students} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE sec.adviser_id = ? AND " . StudentScope::TRASHED;
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $teacherId);
                $stmt->execute();
                return array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'id');
            }catch(Exception $e){
                error_log("Error listing trashed ids: " . $e->getMessage());
                return [];
            }
        }

        /** Rows past the retention window, regardless of owner -- for auto-purge. */
        public function getExpired($retentionDays){
            try{
                $query = "SELECT id, picture FROM {$this->students} WHERE deleted_at IS NOT NULL AND deleted_at < (NOW() - INTERVAL ? DAY)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $retentionDays);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching expired trash: " . $e->getMessage());
                return [];
            }
        }
    }
