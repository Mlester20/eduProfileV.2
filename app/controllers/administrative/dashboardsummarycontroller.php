<?php
// admindashboardcontroller.php (required below) already calls session_start()
// itself — calling it again here would emit a notice that corrupts the JSON
// response body before the browser can parse it.
require_once __DIR__ . '/../../models/administrative/learnerprofilemodel.php';
require_once __DIR__ . '/../../models/administrative/compiledrecordsmodel.php';
require_once __DIR__ . '/../../models/administrative/atriskmodel.php';
require_once __DIR__ . '/../../models/administrative/dashboardsummarymodel.php';
require_once __DIR__ . '/../../models/admin/SchoolYearModel.php';
require_once __DIR__ . '/../../models/admin/SectionsModel.php';
require_once __DIR__ . '/../../models/admin/UsersModel.php';
require_once __DIR__ . '/../../services/GeminiService.php';
require_once __DIR__ . '/../../helpers/csrf.php';
require_once __DIR__ . '/../../helpers/auditLogs.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/admindashboardcontroller.php';

AuthRole::allowOnly(['administrative']);

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

Csrf::requireValidJson($_POST['csrf_token'] ?? null);

try{
    $dashboard = new AdminDashboardController($con);
    $metrics = $dashboard->getSummaryMetrics();
    $activeSyRows = (new SchoolYearModel($con))->getActiveSy();
    $activeSyId = $activeSyRows[0]['id'] ?? null;

    if(!$activeSyId){
        echo json_encode(['success' => false, 'message' => 'No active school year is set.']);
        exit();
    }

    $summary = GeminiService::generateDashboardSummary($metrics);

    if($summary === null){
        echo json_encode(['success' => false, 'message' => 'Could not generate a summary right now. Check that the Gemini API key is configured and try again.']);
        exit();
    }

    $summaryModel = new DashboardSummaryModel($con);
    $summaryModel->saveSummary($activeSyId, $summary);

    $auditLogs = new AuditLogs($con);
    $auditLogs->log(
        $_SESSION['id'] ?? null,
        $_SESSION['role'] ?? 'unknown',
        'Generated AI dashboard summary',
        'Students',
        null,
        null,
        ($_SESSION['full_name'] ?? 'Administrative') . ' generated an AI dashboard summary for school year ID: ' . $activeSyId
    );

    echo json_encode(['success' => true, 'summary' => $summary]);
}catch(Exception $e){
    error_log("Error in DashboardSummaryController: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unexpected error generating summary.']);
}
