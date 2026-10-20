<?php
session_start();

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/PasswordResetModel.php';
require_once __DIR__ . '/../models/admin/UsersModel.php';
require_once __DIR__ . '/../core/BaseUrl.php';
require_once __DIR__ . '/../helpers/flashMessage.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/password.php';
require_once __DIR__ . '/../../database/config/config.php';

const VERIFIED_WINDOW_SECONDS = 600; // matches the 10-minute code TTL

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    header('Location: ' . base_url('reset-password.php'));
    exit();
}

Csrf::requireValidOnPost(base_url('reset-password.php'));

$verifiedUserId = $_SESSION['pwreset_verified_user_id'] ?? null;
$verifiedAt = $_SESSION['pwreset_verified_at'] ?? null;
$stillValid = $verifiedUserId && $verifiedAt && (time() - $verifiedAt) <= VERIFIED_WINDOW_SECONDS;

if(!$stillValid){
    FlashMessage::setFlash('error', 'Your session to reset the password has expired. Please start again.');
    header('Location: ' . base_url('forgot-password.php'));
    exit();
}

$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if(strlen($newPassword) < 8){
    FlashMessage::setFlash('error', 'New password must be at least 8 characters.');
    header('Location: ' . base_url('reset-password.php'));
    exit();
}

if(!hash_equals($newPassword, $confirmPassword)){
    FlashMessage::setFlash('error', 'Passwords do not match.');
    header('Location: ' . base_url('reset-password.php'));
    exit();
}

try{
    $usersModel = new UsersModel($con);
    $resetModel = new PasswordResetModel($con);

    $hashed = HashPassword::passwordHash($newPassword);
    $usersModel->resetPassword($verifiedUserId, $hashed);
    $resetModel->invalidateAllForUser($verifiedUserId);

    unset($_SESSION['pwreset_email'], $_SESSION['pwreset_verified_user_id'], $_SESSION['pwreset_verified_at']);

    // If this same browser happens to already be logged in as the account
    // that just reset its password, force it to log back in with the new one.
    if(isset($_SESSION['id']) && (int) $_SESSION['id'] === (int) $verifiedUserId){
        session_unset();
        session_destroy();
        session_start();
    }

    FlashMessage::setFlash('success', 'Your password has been reset. Please log in with your new password.');
    header('Location: ' . base_url('index.php'));
    exit();
}catch(Exception $e){
    error_log("Error in ResetPassword: " . $e->getMessage());
    FlashMessage::setFlash('error', 'Something went wrong while resetting your password. Please try again.');
    header('Location: ' . base_url('reset-password.php'));
    exit();
}
