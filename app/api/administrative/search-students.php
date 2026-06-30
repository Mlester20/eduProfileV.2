<?php
session_start();

require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';

AuthRole::allowOnly(['administrative']);

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

if (mb_strlen($q) < 2) {
    echo json_encode(['success' => true, 'students' => []]);
    exit();
}

$search = '%' . $q . '%';

$stmt = $con->prepare("
    SELECT
        s.id,
        s.lrn,
        s.grade_level,
        s.section,
        s.enrollment_status,
        CONCAT(
            s.last_name, ', ', s.first_name,
            IF(s.middle_name IS NOT NULL AND s.middle_name != '', CONCAT(' ', s.middle_name), ''),
            IF(s.suffix IS NOT NULL AND s.suffix != '', CONCAT(' ', s.suffix), '')
        ) AS full_name
    FROM students s
    WHERE
        s.lrn LIKE ?
        OR s.first_name LIKE ?
        OR s.last_name LIKE ?
        OR CONCAT(s.first_name, ' ', s.last_name) LIKE ?
        OR CONCAT(s.last_name, ' ', s.first_name) LIKE ?
    ORDER BY s.last_name ASC, s.first_name ASC
    LIMIT 15
");
$stmt->bind_param('sssss', $search, $search, $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode(['success' => true, 'students' => $students]);
