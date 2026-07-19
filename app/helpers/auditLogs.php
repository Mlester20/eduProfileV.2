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

        /**
         * Builds the shared WHERE clause + bind params for the audit log
         * browser's filters, so getPageFiltered()/countFiltered() stay in
         * sync instead of duplicating the filter logic twice.
         */
        private function buildFilterClause($filters) {
            $where = [];
            $types = '';
            $params = [];

            if (!empty($filters['module'])) {
                $where[] = 'al.module = ?';
                $types .= 's';
                $params[] = $filters['module'];
            }
            if (!empty($filters['role'])) {
                $where[] = 'al.role = ?';
                $types .= 's';
                $params[] = $filters['role'];
            }
            if (!empty($filters['date_from'])) {
                $where[] = 'al.created_at >= ?';
                $types .= 's';
                $params[] = $filters['date_from'] . ' 00:00:00';
            }
            if (!empty($filters['date_to'])) {
                $where[] = 'al.created_at <= ?';
                $types .= 's';
                $params[] = $filters['date_to'] . ' 23:59:59';
            }
            if (!empty($filters['search'])) {
                $where[] = '(al.action LIKE ? OR al.description LIKE ? OR u.full_name LIKE ?)';
                $like = '%' . $filters['search'] . '%';
                $types .= 'sss';
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }

            $clause = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
            return [$clause, $types, $params];
        }

        /** Paginated, filterable audit log listing for the admin System Audit Log page. */
        public function getPageFiltered($limit, $offset, $filters = []) {
            try {
                [$whereClause, $types, $params] = $this->buildFilterClause($filters);

                $query = "SELECT al.*, u.full_name AS actor_name
                    FROM audit_logs al
                    LEFT JOIN users u ON al.user_id = u.id
                    {$whereClause}
                    ORDER BY al.created_at DESC, al.id DESC
                    LIMIT ? OFFSET ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types . 'ii', ...[...$params, $limit, $offset]);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } catch (Exception $e) {
                error_log("Error fetching filtered audit logs: " . $e->getMessage());
                return [];
            }
        }

        public function countFiltered($filters = []) {
            try {
                [$whereClause, $types, $params] = $this->buildFilterClause($filters);

                $query = "SELECT COUNT(*) AS total
                    FROM audit_logs al
                    LEFT JOIN users u ON al.user_id = u.id
                    {$whereClause}";
                $stmt = $this->con->prepare($query);
                if ($types) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                return (int) ($row['total'] ?? 0);
            } catch (Exception $e) {
                error_log("Error counting filtered audit logs: " . $e->getMessage());
                return 0;
            }
        }

        /** Distinct module values, for the audit log page's Module filter dropdown. */
        public function getDistinctModules() {
            try {
                $result = $this->con->query("SELECT DISTINCT module FROM audit_logs ORDER BY module ASC");
                return array_column($result->fetch_all(MYSQLI_ASSOC), 'module');
            } catch (Exception $e) {
                error_log("Error fetching distinct modules: " . $e->getMessage());
                return [];
            }
        }
    }

?>