<?php
require_once __DIR__ . '/../core/Model.php';

    class AuditLogs extends Model {
        public function log($user_id, $role, $action, $module, $reference_id = null, $reference_table = null, $description = '', $status = 'success') {
            try {
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

                $query = "INSERT INTO audit_logs (user_id, role, action, module, reference_id, reference_table, description, ip_address, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

                $stmt = $this->con->prepare($query);

                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->con->error);
                }

                $stmt->bind_param(
                    "isssissss",
                    $user_id,
                    $role,
                    $action,
                    $module,
                    $reference_id,
                    $reference_table,
                    $description,
                    $ip,
                    $status
                );

                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }

                $stmt->close();
                return true;

            } catch (Exception $e) {
                error_log("Audit Log Error: " . $e->getMessage());
                return false;
            }
        }

        public function getRecentByUser($user_id, $limit = 10) {
            try {
                $query = "SELECT * FROM audit_logs WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $user_id, $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } catch (Exception $e) {
                error_log("Error fetching recent audit logs: " . $e->getMessage());
                return [];
            }
        }

        /** School-wide activity feed (no user filter) — for the administrative dashboard. */
        public function getRecent($limit = 10) {
            try {
                $query = "SELECT al.*, u.full_name AS actor_name
                    FROM audit_logs al
                    LEFT JOIN users u ON al.user_id = u.id
                    ORDER BY al.created_at DESC, al.id DESC
                    LIMIT ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } catch (Exception $e) {
                error_log("Error fetching recent audit logs: " . $e->getMessage());
                return [];
            }
        }
    }

?>