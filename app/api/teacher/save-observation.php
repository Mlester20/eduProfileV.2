<?php
session_start();

require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';

AuthRole::allowOnly(['teacher']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$studentId  = isset($_POST['student_id'])  ? (int) trim($_POST['student_id'])  : 0;
$category   = isset($_POST['category'])    ? trim($_POST['category'])           : '';
$date       = isset($_POST['date'])        ? trim($_POST['date'])               : '';
$observation = isset($_POST['observation']) ? trim($_POST['observation'])        : '';

// Basic validation
if (!$studentId || !$category || !$date || !$observation) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

if (!in_array($category, ['Behavioral', 'Developmental'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid category selected.']);
    exit();
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format.']);
    exit();
}

// Confirm student is assigned to this teacher and is enrolled
$teacherId = (int) $_SESSION['id'];

$checkStmt = $con->prepare("
    SELECT id FROM students
    WHERE id = ? AND assigned_teacher_id = ? AND enrollment_status = 'Enrolled'
");
$checkStmt->bind_param('ii', $studentId, $teacherId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
if (!$checkResult->fetch_assoc()) {
    $checkStmt->close();
    echo json_encode(['success' => false, 'message' => 'Student not found or not assigned to your account.']);
    exit();
}
$checkStmt->close();

// Get the active school year
$syStmt = $con->prepare("SELECT id FROM school_year WHERE status = 'active' LIMIT 1");
$syStmt->execute();
$syResult = $syStmt->get_result();
$activeSY = $syResult->fetch_assoc();
$syStmt->close();
$activeSchoolYearId = $activeSY ? (int) $activeSY['id'] : 8;

// Insert observation
$insertStmt = $con->prepare("
    INSERT INTO behavior_observations
        (student_id, school_year_id, recorded_by, category, date, observation)
    VALUES (?, ?, ?, ?, ?, ?)
");
$insertStmt->bind_param('iiisss', $studentId, $activeSchoolYearId, $teacherId, $category, $date, $observation);

if ($insertStmt->execute()) {
    $insertStmt->close();
    echo json_encode(['success' => true, 'message' => 'Observation saved successfully.']);
} else {
    $errMsg = $insertStmt->error;
    $insertStmt->close();
    error_log('save-observation error: ' . $errMsg);
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
