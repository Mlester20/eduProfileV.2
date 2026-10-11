<?php
session_start();
require_once __DIR__ . '/../../models/administrative/feedingprogrammodel.php';
require_once __DIR__ . '/../../services/GeminiService.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['teacher']);

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

Csrf::requireValidJson($_POST['csrf_token'] ?? null);

$teacherId = (int) ($_SESSION['id'] ?? 0);
$studentId = isset($_POST['student_id']) ? (int) $_POST['student_id'] : null;
$schoolYearId = isset($_POST['school_year_id']) ? (int) $_POST['school_year_id'] : null;

if(!$studentId || !$schoolYearId){
    echo json_encode(['success' => false, 'message' => 'Missing student or school year.']);
    exit();
}

try{
    $model = new FeedingProgramModel($con);
    // Scoped to this teacher's own advisees — a student who isn't one of
    // theirs simply won't match, same as "not found".
    $metrics = $model->getMetricsForStudent($studentId, $schoolYearId, $teacherId);

    if(!$metrics){
        echo json_encode(['success' => false, 'message' => 'Student not found for the given school year.']);
        exit();
    }

    $insight = GeminiService::generateFeedingProgramInsight($metrics);

    if($insight === null){
        echo json_encode(['success' => false, 'message' => 'Could not generate an insight right now. Check that the Gemini API key is configured and try again.']);
        exit();
    }

    $model->saveInsight($studentId, $schoolYearId, $insight);

    $auditLogs = new AuditLogs($con);
    $auditLogs->log(
        $_SESSION['id'] ?? null,
        $_SESSION['role'] ?? 'unknown',
        'Generated AI feeding program insight',
        'Students',
        $studentId,
        null,
        ($_SESSION['full_name'] ?? 'Teacher') . ' generated an AI feeding program insight for student ID: ' . $studentId
    );

    echo json_encode(['success' => true, 'insight' => $insight]);
}catch(Exception $e){
    error_log("Error in teacher FeedingInsightController: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unexpected error generating insight.']);
}
