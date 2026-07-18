<?php
session_start();

require_once __DIR__ . '/../../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/teacher/AttendanceModel.php';
require_once __DIR__ . '/../../models/teacher/AcademicProfileModel.php';
require_once __DIR__ . '/../../models/teacher/AchievementProfileModel.php';
require_once __DIR__ . '/../../models/teacher/StudentBehavioralProfileModel.php';
require_once __DIR__ . '/../../models/teacher/StudentDevelopmentalProfileModel.php';
require_once __DIR__ . '/../../models/teacher/StudentHealthModel.php';
require_once __DIR__ . '/../../services/StudentService.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['teacher']);

    /**
     * Read-only aggregation for the teacher landing page. Reuses the same
     * per-module models/methods every other teacher page already relies on
     * (adviser + active-status scoping included) rather than introducing new
     * queries — this page just summarizes what those pages already show.
     */

    class DashboardController{
        protected $studentsModel;
        protected $studentService;
        protected $schoolYearModel;
        protected $sectionsModel;
        protected $attendanceModel;
        protected $academicModel;
        protected $achievementModel;
        protected $behavioralModel;
        protected $developmentalModel;
        protected $healthModel;
        protected $auditLogs;

        public function __construct($con){
            $this->studentsModel = new StudentsModel($con);
            $this->studentService = new StudentService($con, $this->studentsModel);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->sectionsModel = new SectionsModel($con);
            $this->attendanceModel = new AttendanceModel($con);
            $this->academicModel = new AcademicProfileModel($con);
            $this->achievementModel = new AchievementProfileModel($con);
            $this->behavioralModel = new StudentBehavioralProfileModel($con);
            $this->developmentalModel = new StudentDevelopmentalProfileModel($con);
            $this->healthModel = new StudentHealthModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        public function getStats(){
            if(!isset($_SESSION['id'])){
                return null;
            }
            $teacherId = (int) $_SESSION['id'];

            $activeSyRows = $this->schoolYearModel->getActiveSy();
            $activeSy = $activeSyRows[0] ?? null;
            $activeSyId = $activeSy['id'] ?? null;

            $students = $this->studentService->getStudentsByAdviser($teacherId, $activeSyId);
            $totalStudents = count($students);

            $mySections = $this->sectionsModel->findByAdviser($teacherId);

            $today = date('Y-m-d');
            $todaysAttendance = $this->attendanceModel->getStudentsForAttendance($teacherId, $today);
            $presentToday = 0;
            $recordedToday = 0;
            foreach($todaysAttendance as $row){
                $hasMorning = $row['morning_status'] !== null;
                $hasAfternoon = $row['afternoon_status'] !== null;
                if($hasMorning || $hasAfternoon){
                    $recordedToday++;
                }
                if($row['morning_status'] === 'Present' || $row['afternoon_status'] === 'Present'){
                    $presentToday++;
                }
            }

            $healthCovered = count($this->healthModel->getStudentIdsWithHealthProfile($teacherId));

            return [
                'active_school_year' => $activeSy,
                'my_sections' => $mySections,
                'total_students' => $totalStudents,
                'attendance_today' => [
                    'date' => $today,
                    'present' => $presentToday,
                    'recorded' => $recordedToday,
                    'total' => $totalStudents,
                ],
                'health_coverage' => [
                    'covered' => $healthCovered,
                    'total' => $totalStudents,
                ],
                'academic_count' => $this->academicModel->countAll($teacherId),
                'achievement_count' => $this->achievementModel->countAll($teacherId),
                'behavioral_count' => $this->behavioralModel->countAll($teacherId),
                'developmental_count' => $this->developmentalModel->countAll($teacherId),
            ];
        }

        public function getRecentActivity($limit = 8){
            if(!isset($_SESSION['id'])){
                return [];
            }
            $logs = $this->auditLogs->getRecentByUser((int) $_SESSION['id'], $limit);
            foreach($logs as &$log){
                $log['description'] = $this->resolveDescription($log);
            }
            return $logs;
        }

        /**
         * The Students controller bakes a bare "with ID: N" into its audit
         * description instead of using reference_id/reference_table, so a
         * raw student id would otherwise leak straight onto the dashboard.
         * Swap it for the student's actual name — or a plain notice if the
         * row is gone, e.g. after a delete — by looking the id up live
         * rather than trusting whatever the log text happened to capture.
         */

        private function resolveDescription($log){
            $description = $log['description'] ?? '';
            if(($log['module'] ?? '') !== 'Students'){
                return $description;
            }
            if(!preg_match('/with ID:\s*(\d+)/', $description, $matches)){
                return $description;
            }
            $name = $this->studentsModel->getFullNameById((int) $matches[1]);
            $replacement = $name ? "({$name})" : '(record no longer available)';
            return trim(preg_replace('/with ID:\s*\d+/', $replacement, $description));
        }
    }
