<?php
require_once __DIR__ . '/../../vendor/autoload.php';

    class MailService{
        private $resend;

        public function __construct(){
            //Load .env variables 
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
            $dotenv->safeLoad();

            $apiKey = $_ENV['RESEND_API_KEY'] ?? '';
            $this->resend = Resend::client($apiKey);
        }

        public function sendPasswordResetCode($toEmail, $code){
            try{
                $this->resend->emails->send([
                    'from' => 'No-Reply <onboarding@resend.dev>',
                    'to' => [$toEmail],
                    'subject' => 'Your EduProfile Password Reset Code',
                    'html' => "
                    <p>Hi,</p>
                    <p>You requested to reset your password. Use the verification code below to continue:</p>
                    <h2 style='letter-spacing: 4px;'>{$code}</h2>
                    <p>This code expires in 10 minutes. If you didn't request this, you can ignore this email.</p>
                    "
                ]);
                return true;
            }catch(Exception $e){
                throw new Exception("Something went wrong while sending the reset code " . $e->getMessage());
                return false;
            }
        }

        public function sendPasswordReset($toEmail, $resetLink){
            try{
                $this->resend->emails->send([
                    'from' => 'No-Reply <onboarding@resend.dev>',
                    'to' => [$toEmail],
                    'subject' => 'Reset Your Password',
                    'html' => "
                    <p>Hi,</p>
                    <p>You requested a password reset. Click the link below to reset your password:</p>
                    <a href='{$resetLink}'>Reset Password</a>
                    <p>If you didn't request this, you can ignore this email.</p>
                    "
                ]);
                return true;
            }catch(Exception $e){
                throw new Exception("Something went wrong while proceesing the request " . $e->getMessage());
                return false;
            }
        }
    }