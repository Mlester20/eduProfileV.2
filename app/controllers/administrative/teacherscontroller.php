<?php
session_start();

require_once __DIR__ . '/../../models/admin/UsersModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../helpers/flashMessage.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../helpers/password.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['administrative']);

    /**
     * Lets administrative staff create/manage teacher accounts directly,
     * instead of having to go through the admin role. Deliberately
     * scoped to role = 'teacher' only — every write here hardcodes that
     * role server-side (never trusts a posted role value), so this
     * screen can never be used to create or promote an admin/
     * administrative account. Full multi-role user management stays
     * admin-only (admin/UsersController.php).
     */

    class TeachersController{
        protected $model;
        protected $auditLogs;
        protected $sectionsModel;

        const ROLE = 'teacher';

        public function __construct($con){
            $this->model = new UsersModel($con);
            $this->auditLogs = new AuditLogs($con);
            $this->sectionsModel = new SectionsModel($con);
        }

        public function index($statusFilter = null){
            return $this->model->indexByRole(self::ROLE, $statusFilter);
        }

        public function show($id){
            return $this->model->getById($id, self::ROLE);
        }

        public function create($data){
            try{
                if(strlen($data['password'] ?? '') < 8){
                    FlashMessage::setFlash('error', 'Password must be at least 8 characters.');
                    header('Location: ../../../resources/views/administrative/teachers.php');
                    exit();
                }
                if(($data['password'] ?? '') !== ($data['confirm_password'] ?? '')){
                    FlashMessage::setFlash('error', 'Passwords do not match.');
                    header('Location: ../../../resources/views/administrative/teachers.php');
                    exit();
                }
                if(!empty($data['contact_number']) && !preg_match('/^\d{11}$/', $data['contact_number'])){
                    FlashMessage::setFlash('error', 'Contact number must be exactly 11 digits.');
                    header('Location: ../../../resources/views/administrative/teachers.php');
                    exit();
                }

                $payload = [
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'contact_number' => $data['contact_number'] ?? null,
                    'address' => $data['address'] ?? null,
                    'province' => $data['province'] ?? null,
                    'city_municipality' => $data['city_municipality'] ?? null,
                    'barangay' => $data['barangay'] ?? null,
                    'birth_date' => $data['birth_date'] ?? null,
                    'sex' => $data['sex'] ?? null,
                    'password' => $data['password'],
                    'role' => self::ROLE,
                    'position' => $data['position'] ?? null,
                ];
                if($this->model->create($payload)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Created user',
                        'Users',
                        null,
                        null,
                        ($_SESSION['full_name'] ?? 'Administrative') . ' created a new teacher account for ' . $data['full_name']
                    );
                    FlashMessage::setFlash('success', 'Teacher account created successfully.');
                }else{
                    FlashMessage::setFlash('error', 'Failed to create teacher account.');
                }
                header('Location: ../../../resources/views/administrative/teachers.php');
                exit();
            }catch(Exception $e){
                error_log($e->getMessage());
                FlashMessage::setFlash('error', 'Failed to create teacher account.');
                header('Location: ../../../resources/views/administrative/teachers.php');
                exit();
            }
        }

        public function update($id, $data){
            try{
                if(!empty($data['contact_number']) && !preg_match('/^\d{11}$/', $data['contact_number'])){
                    FlashMessage::setFlash('error', 'Contact number must be exactly 11 digits.');
                    header('Location: ../../../resources/views/administrative/teachers.php');
                    exit();
                }

                // The legacy free-text `address` field isn't collected by
                // this form anymore (replaced by province/city/barangay) --
                // preserve whatever's already on the record instead of
                // wiping it with null on every edit.
                $existing = $this->model->getById($id, self::ROLE);
                $data['address'] = $existing['address'] ?? null;

                $payload = [
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'contact_number' => $data['contact_number'] ?? null,
                    'address' => $data['address'] ?? null,
                    'province' => $data['province'] ?? null,
                    'city_municipality' => $data['city_municipality'] ?? null,
                    'barangay' => $data['barangay'] ?? null,
                    'birth_date' => $data['birth_date'] ?? null,
                    'sex' => $data['sex'] ?? null,
                    'role' => self::ROLE,
                    'position' => $data['position'] ?? null,
                ];
                if($this->model->update($id, $payload)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        'Updated user',
                        'Users',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'Administrative') . ' updated the teacher account for ' . $data['full_name']
                    );
                    FlashMessage::setFlash('success', 'Teacher account updated successfully.');
                }else{
                    FlashMessage::setFlash('error', 'Failed to update teacher account.');
                }
                header('Location: ../../../resources/views/administrative/teachers.php');
                exit();
            }catch(Exception $e){
                error_log($e->getMessage());
                header('Location: ../../../resources/views/administrative/teachers.php');
                exit();
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
                            ($_SESSION['full_name'] ?? 'Administrative') . ' reset the password for teacher ID: ' . $id
                        );
                        FlashMessage::setFlash('success', 'Password reset successfully.');
                    }else{
                        FlashMessage::setFlash('error', 'Failed to reset password.');
                    }
                }
                header('Location: ../../../resources/views/administrative/teachers.php');
                exit();
            }catch(Exception $e){
                error_log($e->getMessage());
                header('Location: ../../../resources/views/administrative/teachers.php');
                exit();
            }
        }

        public function setStatus($id, $status){
            try{
                // Both deactivating and archiving take a teacher out of
                // active duty, so either one is blocked while they're still
                // an assigned section adviser -- reassign the section(s) first.
                if(in_array($status, ['inactive', 'archived'], true)){
                    $assignedSections = $this->sectionsModel->findByAdviser($id);
                    if(!empty($assignedSections)){
                        $sectionNames = array_map(function($s){
                            return trim(($s['grade_level_name'] ?? '') . ' - ' . $s['section_name']);
                        }, $assignedSections);
                        $verb = $status === 'archived' ? 'archive' : 'deactivate';
                        FlashMessage::setFlash('warning', "Cannot {$verb}: this teacher is still the adviser of " . implode(', ', $sectionNames) . '. Reassign the section(s) first.');
                        header('Location: ../../../resources/views/administrative/teachers.php');
                        exit();
                    }
                }

                $actionLabels = [
                    'active' => ['Activated user', 'activated'],
                    'inactive' => ['Deactivated user', 'deactivated'],
                    'archived' => ['Archived user', 'archived'],
                ];
                [$actionLabel, $actionVerb] = $actionLabels[$status] ?? ['Updated user status', 'updated the status of'];

                if($this->model->setStatus($id, $status)){
                    $this->auditLogs->log(
                        $_SESSION['id'] ?? null,
                        $_SESSION['role'] ?? 'unknown',
                        $actionLabel,
                        'Users',
                        $id,
                        null,
                        ($_SESSION['full_name'] ?? 'Administrative') . ' ' . $actionVerb . ' teacher ID: ' . $id
                    );
                    FlashMessage::setFlash('success', 'Teacher status updated successfully.');
                }else{
                    FlashMessage::setFlash('error', 'Failed to update teacher status.');
                }
                header('Location: ../../../resources/views/administrative/teachers.php');
                exit();
            }catch(Exception $e){
                error_log($e->getMessage());
                header('Location: ../../../resources/views/administrative/teachers.php');
                exit();
            }
        }
    }

    try{
        $controller = new TeachersController($con);
        $status_filter = ($_GET['status'] ?? '') === 'archived' ? 'archived' : 'active';
        $teachers = $controller->index($status_filter);
    }catch(Exception $e){
        error_log("Error in TeachersController: " . $e->getMessage());
        $teachers = [];
        $status_filter = 'active';
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        Csrf::requireValidOnPost('../../../resources/views/administrative/teachers.php');
        if(isset($_POST['createUser'])){
            $controller->create([
                'full_name' => $_POST['full_name'],
                'email' => $_POST['email'],
                'contact_number' => $_POST['contact_number'] ?? null,
                'province' => $_POST['province'] ?? null,
                'city_municipality' => $_POST['city_municipality'] ?? null,
                'barangay' => $_POST['barangay'] ?? null,
                'birth_date' => $_POST['birth_date'] ?? null,
                'sex' => $_POST['sex'] ?? null,
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'position' => $_POST['position'] ?? null,
            ]);
        }
        if(isset($_POST['updateUser'])){
            $controller->update($_POST['id'], [
                'full_name' => $_POST['full_name'],
                'email' => $_POST['email'],
                'contact_number' => $_POST['contact_number'] ?? null,
                'province' => $_POST['province'] ?? null,
                'city_municipality' => $_POST['city_municipality'] ?? null,
                'barangay' => $_POST['barangay'] ?? null,
                'birth_date' => $_POST['birth_date'] ?? null,
                'sex' => $_POST['sex'] ?? null,
                'position' => $_POST['position'] ?? null,
            ]);
        }
        if(isset($_POST['resetUserPassword'])){
            $controller->resetPassword(
                $_POST['id'],
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? ''
            );
        }
        if(isset($_POST['toggleUserStatus'])){
            $controller->setStatus($_POST['id'], $_POST['status']);
        }
    }
