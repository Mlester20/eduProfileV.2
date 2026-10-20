<?php
require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../../helpers/password.php';

    class UsersModel extends Model{
        protected $users = 'users';

        public function index(){
            try{
                $query = "SELECT id, full_name, email, contact_number, address, birth_date, sex, role, status FROM {$this->users} ORDER BY id ASC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $users = $stmt->get_result();
                return $users->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log($e->getMessage());
                exit();
            }
        }

        /**
         * $statusFilter 'archived' shows only archived accounts; anything
         * else (including null/default) shows active + inactive but
         * excludes archived — archived is a separate "left for good" state
         * from the active/inactive login-access toggle, hidden from the
         * default roster the same way archived students are.
         */

        public function indexByRole($role, $statusFilter = null){
            try{
                $query = "SELECT id, full_name, email, contact_number, address, birth_date, sex, role, status, position FROM {$this->users} WHERE role = ?";
                $types = "s";
                $params = [$role];
                if($statusFilter === 'archived'){
                    $query .= " AND status = 'archived'";
                }else{
                    $query .= " AND status != 'archived'";
                }
                $query .= " ORDER BY full_name ASC";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log($e->getMessage());
                return [];
            }
        }

        public function getById($id, $role = null){
            try{
                $query = "SELECT id, full_name, email, contact_number, address, birth_date, sex, role, status, position, profile_picture, created_at FROM {$this->users} WHERE id = ?";
                $types = "i";
                $params = [$id];
                if($role !== null){
                    $query .= " AND role = ?";
                    $types .= "s";
                    $params[] = $role;
                }
                $stmt = $this->con->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log($e->getMessage());
                return null;
            }
        }

        public function getAvailableTeachers(){
            try{
                $query = "SELECT id, full_name, role FROM {$this->users} WHERE role = 'teacher' AND status = 'active' ORDER BY full_name ASC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return [];
            }
        }

        public function create($data){
            try{
                $query = "INSERT INTO {$this->users} (full_name, email, contact_number, address, birth_date, sex, password, role, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($query);
                $hashedPassword = HashPassword::passwordHash($data['password']);
                if(!$stmt){
                    throw new Exception("Error preparing statement");
                }
                $contactNumber = $data['contact_number'] ?? null;
                $address = $data['address'] ?? null;
                $birthDate = !empty($data['birth_date']) ? $data['birth_date'] : null;
                $sex = $data['sex'] ?? null;
                $position = $data['position'] ?? null;
                $stmt->bind_param(
                    "sssssssss",
                    $data['full_name'],
                    $data['email'],
                    $contactNumber,
                    $address,
                    $birthDate,
                    $sex,
                    $hashedPassword,
                    $data['role'],
                    $position
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log($e->getMessage());
                exit();
            }
        }

        public function update($id, $data){
            try{
                $query = "UPDATE {$this->users} SET full_name = ?, email = ?, contact_number = ?, address = ?, birth_date = ?, sex = ?, role = ?, position = ? WHERE id = ?";
                $stmt = $this->con->prepare($query);
                if(!$stmt){
                    throw new Exception("Error preparing statement");
                }
                $contactNumber = $data['contact_number'] ?? null;
                $address = $data['address'] ?? null;
                $birthDate = !empty($data['birth_date']) ? $data['birth_date'] : null;
                $sex = $data['sex'] ?? null;
                $position = $data['position'] ?? null;
                $stmt->bind_param(
                    "ssssssssi",
                    $data['full_name'],
                    $data['email'],
                    $contactNumber,
                    $address,
                    $birthDate,
                    $sex,
                    $data['role'],
                    $position,
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log($e->getMessage());
                exit();
            }
        }

        public function delete($id){
            try{
                $query = "DELETE FROM {$this->users} WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log($e->getMessage());
                exit();
            }
        }

        public function resetPassword($id, $hashedPassword){
            try{
                $query = "UPDATE {$this->users} SET password = ? WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("si", $hashedPassword, $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function setStatus($id, $status){
            try{
                $query = "UPDATE {$this->users} SET status = ? WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("si", $status, $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log($e->getMessage());
                return false;
            }
        }
    }

?>