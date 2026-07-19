<?php
session_start();

require_once __DIR__ . '/../../models/admin/UsersModel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/admin/GradeLevelsModel.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['admin']);

    /**
     * System-configuration aggregation for the admin landing page — admin
     * owns accounts/school-year/grade-level/section setup, not the
     * academic records themselves (that's the administrative role), so
     * the stats here are about the health of that configuration data.
     */

    class DashboardController{
        protected $usersModel;
        protected $schoolYearModel;
        protected $sectionsModel;
        protected $gradeLevelsModel;
        protected $auditLogs;

        public function __construct($con){
            $this->usersModel = new UsersModel($con);
            $this->schoolYearModel = new SchoolYearModel($con);
            $this->sectionsModel = new SectionsModel($con);
            $this->gradeLevelsModel = new GradeLevelsModel($con);
            $this->auditLogs = new AuditLogs($con);
        }

        public function getStats(){
            $users = $this->usersModel->index();
            $usersByRole = array_count_values(array_column($users, 'role'));

            $schoolYears = $this->schoolYearModel->index();
            $activeSyRows = $this->schoolYearModel->getActiveSy();
            $activeSy = $activeSyRows[0] ?? null;

            $sections = $this->sectionsModel->index();
            $sectionsWithoutAdviser = array_values(array_filter($sections, function($s){
                return empty($s['adviser_id']);
            }));

            $gradeLevels = $this->gradeLevelsModel->index();

            return [
                'total_users' => count($users),
                // Preserves every role actually present in the table (not just
                // admin/administrative/teacher) so this always reconciles with
                // total_users even if a stray/legacy role value exists.
                'users_by_role' => $usersByRole,
                'total_school_years' => count($schoolYears),
                'active_school_year' => $activeSy,
                'total_sections' => count($sections),
                'sections_without_adviser' => $sectionsWithoutAdviser,
                'total_grade_levels' => count($gradeLevels),
            ];
        }

        public function getRecentActivity($limit = 8){
            return $this->auditLogs->getRecent($limit);
        }
    }
