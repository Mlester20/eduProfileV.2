<?php
session_start();

require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';

AuthRole::allowOnly(['administrative']);

header('Content-Type: application/json');

$studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;

if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required.']);
    exit();
}

// ---- Fetch student with guardian and teacher ----
$stmt = $con->prepare("
    SELECT
        s.id,
        s.lrn,
        s.first_name,
        s.middle_name,
        s.last_name,
        s.suffix,
        CONCAT(
            s.last_name, ', ', s.first_name,
            IF(s.middle_name IS NOT NULL AND s.middle_name != '', CONCAT(' ', s.middle_name), ''),
            IF(s.suffix   IS NOT NULL AND s.suffix   != '', CONCAT(' ', s.suffix),   '')
        ) AS full_name,
        s.grade_level,
        s.section,
        s.gender,
        s.birth_date,
        s.age,
        s.place_of_birth,
        s.nationality,
        s.religion,
        s.address,
        s.contact_number,
        s.email,
        s.enrollment_status,
        s.profile_photo,
        -- Guardian columns
        g.id           AS guardian_id,
        g.first_name   AS g_first_name,
        g.middle_name  AS g_middle_name,
        g.last_name    AS g_last_name,
        g.relationship AS g_relationship,
        g.contact_number AS g_contact_number,
        g.occupation   AS g_occupation,
        -- Teacher name
        u.full_name    AS teacher_name
    FROM students s
    LEFT JOIN guardians g ON s.guardian_id = g.id
    LEFT JOIN users u     ON s.assigned_teacher_id = u.id
    WHERE s.id = ?
    LIMIT 1
");
$stmt->bind_param('i', $studentId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Student not found.']);
    exit();
}

$student = [
    'id'               => $row['id'],
    'lrn'              => $row['lrn'],
    'full_name'        => $row['full_name'],
    'first_name'       => $row['first_name'],
    'middle_name'      => $row['middle_name'],
    'last_name'        => $row['last_name'],
    'suffix'           => $row['suffix'],
    'grade_level'      => $row['grade_level'],
    'section'          => $row['section'],
    'gender'           => $row['gender'],
    'birth_date'       => $row['birth_date'],
    'age'              => $row['age'],
    'place_of_birth'   => $row['place_of_birth'],
    'nationality'      => $row['nationality'],
    'religion'         => $row['religion'],
    'address'          => $row['address'],
    'contact_number'   => $row['contact_number'],
    'email'            => $row['email'],
    'enrollment_status'=> $row['enrollment_status'],
    'teacher_name'     => $row['teacher_name'],
];

$guardian = null;
if (!empty($row['guardian_id'])) {
    $gFullName = trim(implode(' ', array_filter([
        $row['g_first_name'],
        $row['g_middle_name'],
        $row['g_last_name'],
    ])));
    $guardian = [
        'full_name'      => $gFullName,
        'first_name'     => $row['g_first_name'],
        'middle_name'    => $row['g_middle_name'],
        'last_name'      => $row['g_last_name'],
        'relationship'   => $row['g_relationship'],
        'contact_number' => $row['g_contact_number'],
        'occupation'     => $row['g_occupation'],
    ];
}

// ---- Fetch ALL observations (with school year and recorder name) ----
$obsStmt = $con->prepare("
    SELECT
        bo.id,
        bo.category,
        bo.date,
        bo.observation,
        bo.created_at,
        sy.school_year,
        u.full_name AS recorded_by_name
    FROM behavior_observations bo
    LEFT JOIN school_year sy ON bo.school_year_id = sy.id
    LEFT JOIN users u        ON bo.recorded_by    = u.id
    WHERE bo.student_id = ?
    ORDER BY bo.date DESC, bo.created_at DESC
");
$obsStmt->bind_param('i', $studentId);
$obsStmt->execute();
$obsResult = $obsStmt->get_result();
$observations = $obsResult->fetch_all(MYSQLI_ASSOC);
$obsStmt->close();

echo json_encode([
    'success'      => true,
    'student'      => $student,
    'guardian'     => $guardian,
    'observations' => $observations,
]);
