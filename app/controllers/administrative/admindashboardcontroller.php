<?php
session_start();

require_once __DIR__ . '/../../models/administrative/learnerprofilemodel.php';
require_once __DIR__ . '/../../models/administrative/compiledrecordsmodel.php';
require_once __DIR__ . '/../../models/administrative/atriskmodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/admin/UsersModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    /**
     * School-wide aggregation for the administrative landing page — no
     * adviser/teacher filter anywhere, same access model as Compiled
     * Records and Learner Profile. Reuses those models' existing queries
     * rather than introducing new ones.
     */

    class AdminDashboardController{
        protected $learnerProfileModel;
        protected $compiledRecordsModel;
        protected $atRiskModel;
        protected $schoolYearModel;
        protected $sectionsModel;
        protected $usersModel;
        protected $auditLogs;

        public function __construct($con){
            $this->learnerProfileModel = new LearnerProfileModel($con);
            $this->compiledRecordsModel = new CompiledRecordsModel($con);
            $this->atRiskModel = new AtRiskModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->sectionsModel = new SectionsModel($con);
            $this->usersModel = new UsersModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        public function getStats(){
            $activeSyRows = $this->schoolYearModel->getActiveSy();
            $activeSy = $activeSyRows[0] ?? null;
            $activeSyId = $activeSy['id'] ?? null;

            $sections = $this->sectionsModel->index();
            $sectionsWithoutAdviser = array_values(array_filter($sections, function($s){
                return empty($s['adviser_id']);
            }));

            $teacherCount = count($this->usersModel->getAvailableTeachers());

            return [
                'active_school_year' => $activeSy,
                'total_sections' => count($sections),
                'sections_without_adviser' => $sectionsWithoutAdviser,
                'total_teachers' => $teacherCount,
                'active_learners' => $this->learnerProfileModel->countAll('active'),
                'archived_learners' => $this->learnerProfileModel->countAll('archived'),
                'at_risk_count' => $this->atRiskModel->countAtRisk($activeSyId),
                'records' => [
                    'Academic' => $this->compiledRecordsModel->countAcademicRecords($activeSyId),
                    'Behavioral' => $this->compiledRecordsModel->countBehavioralRecords($activeSyId),
                    'Developmental' => $this->compiledRecordsModel->countDevelopmentalRecords($activeSyId),
                    'Health' => $this->compiledRecordsModel->countHealthRecords($activeSyId),
                    'Attendance' => $this->compiledRecordsModel->countAttendanceRecords($activeSyId),
                    'Achievements' => $this->compiledRecordsModel->countAchievementRecords($activeSyId),
                ],
            ];
        }

        public function getRecentActivity($limit = 8){
            return $this->auditLogs->getRecent($limit);
        }
    }
