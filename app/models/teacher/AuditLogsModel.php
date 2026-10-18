<?php
require_once __DIR__ . '/../../core/Model.php';

    class AuditLogsModel extends Model{
        protected $audit_logs = 'audit_logs';
        protected $users = 'users';
        
        public function getAudit($user_id){
            try{
                $query = "SELECT 
                    al.*,
                    u.full_name AS teacher_full_name
                    FROM {$this->audit_logs} al
                    LEFT JOIN {$this->users} u ON al.user_id = u.id
                    WHERE al.user_id = ?
                    ORDER BY created_at
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Something went wrong try again. " . $e->getMessage());
            }
        }
    }