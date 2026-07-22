<?php
require_once __DIR__ . '/../helpers/flashMessage.php';
require_once __DIR__ . '/../core/BaseUrl.php';

    class AuthRole{
        public static function isAuthenticated(){
            if(!isset($_SESSION['id'])){
                FlashMessage::setFlash("warning", "Please log in to access this page.");
                header('Location: ' . base_url('index.php'));
                exit();
            }
        }

        public static function allowOnly($allowed_roles = []){
            self::isAuthenticated();

            if(!in_array($_SESSION['role'], $allowed_roles)){
                FlashMessage::setFlash("error", "You do not have permission to access this page.");
                header('Location: ' . base_url('index.php'));
                exit();
            }
        }
    }