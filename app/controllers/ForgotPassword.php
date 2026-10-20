<?php
session_start();

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/PasswordResetModel.php';
require_once __DIR__ . '/../core/MailService.php';
require_once __DIR__ . '/../core/BaseUrl.php';
require_once __DIR__ . '/../helpers/flashMessage.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../../database/config/config.php';

const RESET_COOLDOWN_SECONDS = 60;
const RESET_CODE_TTL_MINUTES = 10;

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    header('Location: ' . base_url('forgot-password.php'));
    exit();
}

Csrf::requireValidOnPost(base_url('forgot-password.php'));

$email = trim($_POST['email'] ?? '');

$genericMessage = 'If an account exists for that email, a verification code has been sent to it.';

if($email === ''){
    FlashMessage::setFlash('error', 'Please enter your email address.');
    header('Location: ' . base_url('forgot-password.php'));
    exit();
}

try{
    $authModel = new AuthModel($con);
    $resetModel = new PasswordResetModel($con);

    $user = $authModel->getUserByEmail($email);

    if($user){
        $withinCooldown = $resetModel->isWithinCooldown($user['id'], RESET_COOLDOWN_SECONDS);

        if(!$withinCooldown){
            $code = (string) random_int(100000, 999999);
            $codeHash = password_hash($code, PASSWORD_BCRYPT, ['cost' => 10]);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;

            $resetModel->createCode($user['id'], $codeHash, RESET_CODE_TTL_MINUTES, $ipAddress);

            $mailer = new MailService();
            $mailer->sendPasswordResetCode($user['email'], $code);
        }
    }

    // Always store the submitted email and proceed to the verify step,
    // regardless of whether an account exists -- revealing that
    // difference here is exactly the enumeration hole the spec calls out.
    $_SESSION['pwreset_email'] = $email;
    unset($_SESSION['pwreset_verified_user_id'], $_SESSION['pwreset_verified_at']);

    FlashMessage::setFlash('success', $genericMessage);
    header('Location: ' . base_url('verify-reset-code.php'));
    exit();
}catch(Exception $e){
    error_log("Error in ForgotPassword: " . $e->getMessage());
    FlashMessage::setFlash('success', $genericMessage);
    $_SESSION['pwreset_email'] = $email;
    header('Location: ' . base_url('verify-reset-code.php'));
    exit();
}
