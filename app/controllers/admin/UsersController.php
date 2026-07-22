<?php
session_start();

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/admin/UsersModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/password.php';
require_once __DIR__ . '/../../../database/config/config.php';
// require_once __DIR__ . '/../../middleware/Role.php';

    class UsersController extends Controller{
        protected $auditLogs;

        public function __construct($con) {
            parent::__construct(new UsersModel($con)); // $this->model is set by the parent
            $this->auditLogs = new AuditLogs($con);
        }

        public function index(){
            try{
                return $this->model->index();
            }catch(Exception $e){
                error_log($e->getMessage());
                exit();
            }
        }

        public function create($data){
            try{
                if($this->model->create($data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Created user',
                        'Users',
                        null,
                        null,
                        ($_SESSION['full_name'] ?? 'Admin') . ' created a new ' . $data['role'] . ' account for ' . $data['full_name']
                    );
                    FlashMessage::setFlash('success', 'User created successfully');
                    header('Location: ../../../resources/views/admin/users.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to create user');
                    header('Location: ../../../resources/views/admin/users.php');
                    exit();
                }
            }catch(Exception $e){
                error_log($e->getMessage());
                return ['success' => false, 'message' => 'Failed to create user.'];
            }
        }

        public function update($id, $data){
            try{
                if($this->model->update($id, $data)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updated user',
                        'Users',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'Admin') . ' updated the account for ' . $data['full_name']
                    );
                    FlashMessage::setFlash('success', 'User updated successfully');
                    header('Location: ../../../resources/views/admin/users.php');
                    exit();
                }else{
                    FlashMessage::setFlash('error', 'Failed to update user');
                    header('Location: ../../../resources/views/admin/users.php');
                    exit();
                }
            }catch(Exception $e){
                error_log($e->getMessage());
            }
        }

        public function resetPassword($id, $newPassword, $confirmPassword){
            try{
                if(strlen($newPassword) < 8){
                    FlashMessage::setFlash('error', 'New password must be at least 8 characters.');
                }elseif($newPassword !== $confirmPassword){
                    FlashMessage::setFlash('error', 'Passwords do not match.');
                }else{
                    $hashed = HashPassword::passwordHash($newPassword);
                    if($this->model->resetPassword($id, $hashed)){
                        $this->auditLogs->log(
                            $_SESSION['id'] ?? null,
                            $_SESSION['role'] ?? 'unknown',
                            'Reset user password',
                            'Users',
                            $id,
                            null,
                            ($_SESSION['full_name'] ?? 'Admin') . ' reset the password for user ID: ' . $id
                        );
                        FlashMessage::setFlash('success', 'Password reset successfully.');
                    }else{
                        FlashMessage::setFlash('error', 'Failed to reset password.');
                    }
                }
                header('Location: ../../../resources/views/admin/users.php');
                exit();
            }catch(Exception $e){
                error_log($e->getMessage());
                exit();
            }
        }

        public function setStatus($id, $status){
            try{
                if($this->model->setStatus($id, $status)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        $status === 'active' ? 'Activated user' : 'Deactivated user',
                        'Users',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'Admin') . ' ' . ($status === 'active' ? 'activated' : 'deactivated') . ' user ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'User status updated successfully.');
                }else{
                    FlashMessage::setFlash('error', 'Failed to update user status.');
                }
                header('Location: ../../../resources/views/admin/users.php');
                exit();
            }catch(Exception $e){
                error_log($e->getMessage());
                exit();
            }
        }

        // Required by the abstract Controller base class. Hard-delete is
        // intentionally no longer wired to any form/action here — accounts
        // are deactivated via setStatus() instead, so historical FK
        // references (audit_logs, recorded_by, adviser_id, etc.) stay intact.
        public function delete($id){
            return $this->model->delete($id);
        }
    }

    // bootstrap model and controller
    try{
        $usersController = new UsersController($con);
        $users = $usersController->index();
    }catch(Exception $e){
        error_log($e->getMessage());
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        Csrf::requireValidOnPost('../../../resources/views/admin/users.php');
        if(isset($_POST['createUser'])){
            $usersController->create(
                [
                    'full_name' => $_POST['full_name'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'role' => $_POST['role']
                ]
            );
        }
        if(isset($_POST['updateUser'])){
            $usersController->update(
                $user_id = $_POST['id'],
                [
                    'full_name' => $_POST['full_name'],
                    'email' => $_POST['email'],
                    'role' => $_POST['role']
                ]
            );
        }
        if(isset($_POST['resetUserPassword'])){
            $usersController->resetPassword(
                $_POST['id'],
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? ''
            );
        }
        if(isset($_POST['toggleUserStatus'])){
            $usersController->setStatus($_POST['id'], $_POST['status']);
        }
    }

?>