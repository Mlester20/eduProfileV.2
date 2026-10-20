<?php
require_once __DIR__ . '/../core/Model.php';

    class PasswordResetModel extends Model{
        protected $table = 'password_reset_codes';

        public function getMostRecentRequest($userId){
            try{
                $query = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY id DESC LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                return $stmt->get_result()->fetch_assoc();
            }catch(Exception $e){
                error_log($e->getMessage());
                return false;
            }
        }

        /**
         * Cooldown/expiry comparisons are done against MySQL's own NOW()
         * rather than PHP's time()/date() -- this app's PHP timezone and
         * the DB server's clock aren't guaranteed to match, and comparing
         * a PHP-computed timestamp against a MySQL-stored one caused codes
         * to look instantly expired (or cooldowns to look falsely active)
         * when the two clocks disagreed.
         */
        public function isWithinCooldown($userId, $cooldownSeconds){
            try{
                $query = "SELECT COUNT(*) AS cnt FROM {$this->table} WHERE user_id = ? AND requested_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ii", $userId, $cooldownSeconds);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                return (int) ($row['cnt'] ?? 0) > 0;
            }catch(Exception $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function invalidateAllForUser($userId){
            try{
                $query = "UPDATE {$this->table} SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $userId);
                return $stmt->execute();
            }catch(Exception $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function createCode($userId, $codeHash, $ttlMinutes, $ipAddress){
            try{
                $this->invalidateAllForUser($userId);
                $query = "INSERT INTO {$this->table} (user_id, code_hash, expires_at, ip_address) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE), ?)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("isis", $userId, $codeHash, $ttlMinutes, $ipAddress);
                return $stmt->execute();
            }catch(Exception $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function getValidCode($userId){
            try{
                $query = "SELECT * FROM {$this->table} WHERE user_id = ? AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                return $stmt->get_result()->fetch_assoc();
            }catch(Exception $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function markUsed($id){
            try{
                $query = "UPDATE {$this->table} SET used_at = NOW() WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $id);
                return $stmt->execute();
            }catch(Exception $e){
                error_log($e->getMessage());
                return false;
            }
        }
    }
?>
