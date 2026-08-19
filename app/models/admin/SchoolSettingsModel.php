<?php
require_once __DIR__ . '/../../core/Model.php';

    class SchoolSettingsModel extends Model{
        protected $school_settings = 'school_settings';

        public function get(){
            try{
                $query = "SELECT * FROM {$this->school_settings} WHERE id = 1 LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching school settings: " . $e->getMessage());
                return null;
            }
        }

        public function update($data, $updatedBy){
            try{
                $query = "UPDATE {$this->school_settings} SET
                    school_name = ?,
                    school_id = ?,
                    region = ?,
                    division = ?,
                    district = ?,
                    school_head = ?,
                    updated_by = ?
                    WHERE id = 1
                ";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param(
                    "ssssssi",
                    $data['school_name'],
                    $data['school_id'],
                    $data['region'],
                    $data['division'],
                    $data['district'],
                    $data['school_head'],
                    $updatedBy
                );
                return $stmt->execute();
            }catch(Exception $e){
                error_log("Error updating school settings: " . $e->getMessage());
                return false;
            }
        }
    }
