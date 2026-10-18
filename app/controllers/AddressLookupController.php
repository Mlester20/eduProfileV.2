<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../models/PsgcModel.php';
require_once __DIR__ . '/../../database/config/config.php';

try{
    header('Content-Type: application/json');

    if(!isset($_SESSION['id'])){
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
        exit();
    }

    $psgc = new PsgcModel($con);
    $level = $_GET['level'] ?? '';

    switch($level){
        case 'provinces':
            echo json_encode(['success' => true, 'data' => $psgc->getProvinces()]);
            break;

        case 'cities':
            $provinceCode = $_GET['province_code'] ?? '';
            if($provinceCode === ''){
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing province_code.']);
                break;
            }
            echo json_encode(['success' => true, 'data' => $psgc->getCitiesByProvince($provinceCode)]);
            break;

        case 'barangays':
            $muncityCode = $_GET['muncity_code'] ?? '';
            if($muncityCode === ''){
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing muncity_code.']);
                break;
            }
            echo json_encode(['success' => true, 'data' => $psgc->getBarangaysByCity($muncityCode)]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown level. Use provinces, cities, or barangays.']);
    }
}catch(Exception $e){
    error_log("Error in AddressLookupController: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
}
