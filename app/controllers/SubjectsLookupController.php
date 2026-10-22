<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../models/admin/SubjectsModel.php';
require_once __DIR__ . '/../../database/config/config.php';

try{
    header('Content-Type: application/json');

    if(!isset($_SESSION['id'])){
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
        exit();
    }

    $gradeLevelId = $_GET['grade_level_id'] ?? '';
    if($gradeLevelId === ''){
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing grade_level_id.']);
        exit();
    }

    $subjectsModel = new SubjectsModel($con);
    echo json_encode(['success' => true, 'data' => $subjectsModel->getAssignedByGradeLevelAndTeacher((int) $gradeLevelId, (int) $_SESSION['id'])]);
}catch(Exception $e){
    error_log("Error in SubjectsLookupController: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
}
