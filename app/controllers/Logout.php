<?php
require_once __DIR__ . '/../core/BaseUrl.php';
session_destroy();
header('Location: ' . base_url('index.php'));
exit();
?>