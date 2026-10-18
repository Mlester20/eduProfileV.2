<?php
require_once __DIR__ . '/../../core/Model.php';

    class GradeLevelsModel extends Model{
        protected $grade_levels = 'grade_levels';
        
        /**
         * Natural pedagogical order (Kindergarten, then Grade 1..N by number)
         * instead of plain alphabetical, since "Grade 10" < "Grade 2" and
         * "Kinder" > "Grade 1" alphabetically — neither matches how a DepEd
         * grade-level list should actually read.
         */

        private static function sortKey($gradeName){
            $name = trim((string) $gradeName);
            if(stripos($name, 'kinder') !== false){
                return [-1, 0, strtolower($name)];
            }
            if(preg_match('/(\d+)/', $name, $matches)){
                return [0, (int) $matches[1], strtolower($name)];
            }
            return [1, 0, strtolower($name)];
        }

        public function index(){
            try{
                $query = "SELECT * FROM {$this->grade_levels}";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                usort($rows, function($a, $b){
                    return self::sortKey($a['grade_name']) <=> self::sortKey($b['grade_name']);
                });

                return $rows;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        /**
         * Case-insensitive lookup, used when a Section is created with a
         * freshly-typed grade level name — reuses the existing row instead
         * of creating a duplicate if it already exists (e.g. "grade 1" vs
         * "Grade 1").
         */

        public function findByName($name){
            try{
                $query = "SELECT * FROM {$this->grade_levels} WHERE LOWER(grade_name) = LOWER(?) LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("s", $name);
                $stmt->execute();
                return $stmt->get_result()->fetch_assoc();
            }catch(Exception $e){
                error_log("Error finding grade level by name: " . $e->getMessage());
                return null;
            }
        }

        public function create($data){
            try{
                $query = "INSERT INTO {$this->grade_levels}(grade_name) VALUES(?)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param(
                    "s",
                    $data['grade_name']
                );
                $stmt->execute();
                return $this->con->insert_id;
            }catch(Exception $e){
                error_log("Error creating grade level " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->grade_levels} set grade_name = ? WHERE id = ? ";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "si",
                    $data['grade_name'],
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error updating Grade Level " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->grade_levels} WHERE id = ? ";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error deleting Grade Level " . $e->getMessage());
                return false;
            }
        }
    }