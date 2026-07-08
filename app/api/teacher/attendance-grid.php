<?php
session_start();

require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/controllers/teacher/AttendanceController.php';

AuthRole::allowOnly(['teacher']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');

$controller = new AttendanceController($con);
echo json_encode($controller->gridData($date));
