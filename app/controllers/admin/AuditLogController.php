<?php
session_start();

require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/Paginator.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['admin']);

    class AuditLogController{
        protected $auditLogs;

        public function __construct($con){
            $this->auditLogs = new AuditLogs($con);
        }

        public function index($page = 1, $filters = []){
            $perPage = 15;
            $offset = Paginator::offset($page, $perPage);
            $rows = $this->auditLogs->getPageFiltered($perPage, $offset, $filters);
            $total = $this->auditLogs->countFiltered($filters);
            return array_merge(['data' => $rows], Paginator::meta($total, $page, $perPage));
        }

        public function getModules(){
            return $this->auditLogs->getDistinctModules();
        }
    }

    try{
        $controller = new AuditLogController($con);
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $filters = [
            'module' => trim($_GET['module'] ?? ''),
            'role' => trim($_GET['role'] ?? ''),
            'date_from' => trim($_GET['date_from'] ?? ''),
            'date_to' => trim($_GET['date_to'] ?? ''),
            'search' => trim($_GET['search'] ?? ''),
        ];
        $result = $controller->index($page, $filters);
        $modules = $controller->getModules();
    }catch(Exception $e){
        error_log("Error in AuditLogController: " . $e->getMessage());
    }
