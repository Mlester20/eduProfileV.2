<?php
require_once __DIR__ . '/../../core/Model.php';

    class AttendanceModel extends Model{
        protected $attendance = 'attendance';
        protected $student = 'students';
        protected $sections = 'sections';
        protected $school_year = 'school_year';
        protected $users = 'users';

        public function index($teacher_id, $student_id = null, $session = null){
            try{
                $query = "SELECT
                    a.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    sy.school_year AS school_year,
                    u.full_name AS recorded_by
                    FROM {$this->attendance} a
                    LEFT JOIN {$this->student} s ON a.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->school_year} sy ON a.school_year_id = sy.id
                    LEFT JOIN {$this->users} u ON a.recorded_by = u.id
                    WHERE sec.adviser_id = ?
                ";
                if($student_id !== null){
                    $query .= " AND a.student_id = ?";
                }
                if($session !== null){
                    $query .= " AND a.session = ?";
                }
                $stmt = $this->con->prepare($query);
                if($student_id !== null && $session !== null){
                    $stmt->bind_param("iis", $teacher_id, $student_id, $session);
                }elseif($student_id !== null){
                    $stmt->bind_param("ii", $teacher_id, $student_id);
                }elseif($session !== null){
                    $stmt->bind_param("is", $teacher_id, $session);
                }else{
                    $stmt->bind_param("i", $teacher_id);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching attendance records: " . $e->getMessage());
                return [];
            }
        }

        public function getHistoryGrouped($teacher_id, $filters = []){
            try{
                $student_id = $filters['student_id'] ?? null;
                $date = $filters['date'] ?? null;
                $status = $filters['status'] ?? null;
                $session = $filters['session'] ?? null;

                $query = "SELECT
                    a.student_id,
                    a.attendance_date,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    MAX(CASE WHEN a.session = 'Morning' THEN a.status END) AS morning_status,
                    MAX(CASE WHEN a.session = 'Afternoon' THEN a.status END) AS afternoon_status,
                    MAX(CASE WHEN a.session = 'Morning' THEN a.remarks END) AS morning_remarks,
                    MAX(CASE WHEN a.session = 'Afternoon' THEN a.remarks END) AS afternoon_remarks
                    FROM {$this->attendance} a
                    LEFT JOIN {$this->student} s ON a.student_id = s.id
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    WHERE sec.adviser_id = ?
                ";

                $types = "i";
                $params = [$teacher_id];

                if($student_id !== null){
                    $query .= " AND a.student_id = ?";
                    $types .= "i";
                    $params[] = $student_id;
                }
                if($date !== null){
                    $query .= " AND a.attendance_date = ?";
                    $types .= "s";
                    $params[] = $date;
                }

                $query .= " GROUP BY a.student_id, a.attendance_date";

                $having = [];
                if($status !== null){
                    $having[] = "(morning_status = ? OR afternoon_status = ?)";
                    $types .= "ss";
                    $params[] = $status;
                    $params[] = $status;
                }
                if($session === 'Morning'){
                    $having[] = "morning_status IS NOT NULL";
                }elseif($session === 'Afternoon'){
                    $having[] = "afternoon_status IS NOT NULL";
                }
                if(!empty($having)){
                    $query .= " HAVING " . implode(" AND ", $having);
                }

                $query .= " ORDER BY a.attendance_date DESC, s.last_name ASC";

                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching grouped attendance history: " . $e->getMessage());
                return [];
            }
        }

        public function create($data){
            try{
                $insert = "INSERT INTO {$this->attendance}(student_id, school_year_id, attendance_date, session, status, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "iissssi",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['attendance_date'],
                    $data['session'],
                    $data['status'],
                    $data['remarks'],
                    $data['recorded_by']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error creating attendance record: " . $e->getMessage());
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->attendance} SET student_id = ?, school_year_id = ?, attendance_date = ?, session = ?, status = ?, remarks = ?, recorded_by = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "iissssii",
                    $data['student_id'],
                    $data['school_year_id'],
                    $data['attendance_date'],
                    $data['session'],
                    $data['status'],
                    $data['remarks'],
                    $data['recorded_by'],
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating attendance record: " . $e->getMessage());
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->attendance} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting attendance record: " . $e->getMessage());
            }
        }

        public function getStudentsForAttendance($teacher_id, $date){
            try{
                $query = "SELECT
                    s.id AS student_id,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    att_am.status AS morning_status,
                    att_am.remarks AS morning_remarks,
                    att_pm.status AS afternoon_status,
                    att_pm.remarks AS afternoon_remarks
                    FROM {$this->student} s
                    LEFT JOIN {$this->sections} sec ON s.section_id = sec.id
                    LEFT JOIN {$this->attendance} att_am ON att_am.student_id = s.id AND att_am.attendance_date = ? AND att_am.session = 'Morning'
                    LEFT JOIN {$this->attendance} att_pm ON att_pm.student_id = s.id AND att_pm.attendance_date = ? AND att_pm.session = 'Afternoon'
                    WHERE sec.adviser_id = ?
                    ORDER BY s.last_name ASC, s.first_name ASC
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ssi", $date, $date, $teacher_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching students for attendance: " . $e->getMessage());
                return [];
            }
        }

        public function getActiveSchoolYearId(){
            try{
                $query = "SELECT id FROM {$this->school_year} WHERE status = 'active' LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                return $row ? (int) $row['id'] : null;
            }catch(Exception $e){
                error_log("Error fetching active school year: " . $e->getMessage());
                return null;
            }
        }

        private function belongsToTeacher($student_id, $teacher_id){
            $query = "SELECT s.id FROM {$this->student} s LEFT JOIN {$this->sections} sec ON s.section_id = sec.id WHERE s.id = ? AND sec.adviser_id = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("ii", $student_id, $teacher_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc() !== null;
        }

        private function attendanceExists($student_id, $date, $session){
            $query = "SELECT id FROM {$this->attendance} WHERE student_id = ? AND attendance_date = ? AND session = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("iss", $student_id, $date, $session);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc() !== null;
        }

        private function insertAttendanceRow($data){
            $insert = "INSERT INTO {$this->attendance}(student_id, school_year_id, attendance_date, session, status, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->con->prepare($insert);
            $stmt->bind_param(
                "iissssi",
                $data['student_id'],
                $data['school_year_id'],
                $data['attendance_date'],
                $data['session'],
                $data['status'],
                $data['remarks'],
                $data['recorded_by']
            );
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
        }

        public function saveBulk($teacher_id, $date, $school_year_id, $records, $recorded_by){
            $inserted = 0;
            $skipped = 0;
            $this->con->begin_transaction();
            try{
                foreach($records as $record){
                    $student_id = (int) ($record['student_id'] ?? 0);
                    $session = $record['session'] ?? '';
                    $status = $record['status'] ?? '';
                    $remarks = $record['remarks'] ?? null;

                    if(!$student_id || !in_array($session, ['Morning', 'Afternoon'], true) || !in_array($status, ['Present', 'Absent', 'Late', 'Excused'], true)){
                        $skipped++;
                        continue;
                    }

                    if(!$this->belongsToTeacher($student_id, $teacher_id)){
                        $skipped++;
                        continue;
                    }

                    if($this->attendanceExists($student_id, $date, $session)){
                        $skipped++;
                        continue;
                    }

                    $data = [
                        'student_id' => $student_id,
                        'school_year_id' => $school_year_id,
                        'attendance_date' => $date,
                        'session' => $session,
                        'status' => $status,
                        'remarks' => $remarks,
                        'recorded_by' => $recorded_by
                    ];

                    $this->insertAttendanceRow($data);
                    $inserted++;
                }
                $this->con->commit();
                return ['inserted' => $inserted, 'skipped' => $skipped];
            }catch(Exception $e){
                $this->con->rollback();
                error_log("Error saving bulk attendance: " . $e->getMessage());
                return false;
            }
        }
    }
