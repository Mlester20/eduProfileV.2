<?php
require_once __DIR__ . '/../../core/Model.php';

    class SchoolYearModel extends Model{
        protected $school_year = 'school_year';

        public function index(){
            try{
                $query = "SELECT * FROM {$this->school_year} ORDER BY id ASC ";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                return $e->getMessage();
            }
        }

        public function getActiveSy(){
            try{
                $query = "SELECT * FROM {$this->school_year} WHERE status = 'active' LIMIT 1";
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
            $query = "INSERT INTO {$this->school_year} (school_year, start_date, end_date) VALUES (?, ?, ?)";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param('sss', $data['school_year'], $data['start_date'], $data['end_date']);
            return $stmt->execute();
          }catch(Exception $e){
            return $e->getMessage();
          }
        }

        public function update($id, $data){
            try{
                $query = "UPDATE {$this->school_year} SET school_year = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('ssssi', $data['school_year'], $data['start_date'], $data['end_date'], $data['status'], $id);
                return $stmt->execute();
            }catch(Exception $e){
                return $e->getMessage();
            }
        }

        public function delete($data){
            try{
                $query = "DELETE FROM {$this->school_year} WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('i', $data['id']);
                return $stmt->execute();
            }catch(Exception $e){
                return $e->getMessage();
            }
        }
    }
