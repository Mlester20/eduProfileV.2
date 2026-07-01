<?php   
require_once __DIR__ . '/../../core/Model.php';

    class ParentGuardianModel extends Model{
        protected $parent_guardian = 'parents_guardians';
        protected $students = 'students';
        protected $users = 'users';

        public function index(){
            try{
                $query = "SELECT
                    pg.*,
                    s.first_name AS student_first_name,
                    s.middle_name AS student_middle_name,
                    s.last_name AS student_last_name,
                    s.suffix AS student_suffix,
                    u.full_name AS recorded_by
                    FROM {$this->parent_guardian} pg
                    LEFT JOIN {$this->students} s ON pg.student_id = s.id
                    LEFT JOIN {$this->users} u ON pg.recorded_by = u.id
                ";
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
                $insert = "INSERT INTO {$this->parent_guardian}(student_id, recorded_by, father_name, father_occupation, father_contact, mother_name, mother_occupation, mother_contact, guardian_name, guardian_relationship, guardian_contact) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param(
                    "iisssssssss",
                    $data['student_id'],
                    $data['recorded_by'],
                    $data['father_name'],
                    $data['father_occupation'],
                    $data['father_contact'],
                    $data['mother_name'],
                    $data['mother_occupation'],
                    $data['mother_contact'],
                    $data['guardian_name'],
                    $data['guardian_relationship'],
                    $data['guardian_contact']
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function update($id, $data){
            try{
                $update = "UPDATE {$this->parent_guardian} SET student_id = ?, recorded_by = ?, father_name = ?, father_occupation = ?, father_contact = ?, mother_name = ?, mother_occupation = ?, mother_contact = ?, guardian_name = ?, guardian_relationship = ?, guardian_contact = ? WHERE id = ?";
                $stmt = $this->con->prepare($update);
                $stmt->bind_param(
                    "iisssssssssi",
                    $data['student_id'],
                    $data['recorded_by'],
                    $data['father_name'],
                    $data['father_occupation'],
                    $data['father_contact'],
                    $data['mother_name'],
                    $data['mother_occupation'],
                    $data['mother_contact'],
                    $data['guardian_name'],
                    $data['guardian_relationship'],
                    $data['guardian_contact'],
                    $id
                );
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }

        public function delete($id){
            try{
                $delete = "DELETE FROM {$this->parent_guardian} WHERE id = ?";
                $stmt = $this->con->prepare($delete);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error " . $e->getMessage());
                return false;
            }
        }
    }