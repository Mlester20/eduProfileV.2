<?php
session_start();

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/PasswordResetModel.php';
require_once __DIR__ . '/../core/BaseUrl.php';
require_once __DIR__ . '/../helpers/flashMessage.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../../database/config/config.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    header('Location: ' . base_url('verify-reset-code.php'));
    exit();
}

Csrf::requireValidOnPost(base_url('verify-reset-code.php'));

$genericError = 'Invalid or expired verification code. Please try again.';

if(empty($_SESSION['pwreset_email'])){
    FlashMessage::setFlash('error', 'Please start the password reset process again.');
    header('Location: ' . base_url('forgot-password.php'));
    exit();
}

$code = trim($_POST['code'] ?? '');

if($code === ''){
    FlashMessage::setFlash('error', 'Please enter the verification code.');
    header('Location: ' . base_url('verify-reset-code.php'));
    exit();
}

try{
    $authModel = new AuthModel($con);
    $resetModel = new PasswordResetModel($con);

    $user = $authModel->getUserByEmail($_SESSION['pwreset_email']);

    if(!$user){
        FlashMessage::setFlash('error', $genericError);
        header('Location: ' . base_url('verify-reset-code.php'));
        exit();
    }

    $validCode = $resetModel->getValidCode($user['id']);

    if(!$validCode || !password_verify($code, $validCode['code_hash'])){
        FlashMessage::setFlash('error', $genericError);
        header('Location: ' . base_url('verify-reset-code.php'));
        exit();
    }

    $resetModel->markUsed($validCode['id']);

    $_SESSION['pwreset_verified_user_id'] = $user['id'];
    $_SESSION['pwreset_verified_at'] = time();

    header('Location: ' . base_url('reset-password.php'));
    exit();
}catch(Exception $e){
    error_log("Error in VerifyResetCode: " . $e->getMessage());
    FlashMessage::setFlash('error', $genericError);
    header('Location: ' . base_url('verify-reset-code.php'));
    exit();
}
