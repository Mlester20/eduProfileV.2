<?php
/**
 * CLI-only dev tool. Inserts (or removes) sample failing-grade, absence,
 * and disciplinary records for one student so the At-Risk Learners page
 * has something to show without manually inserting rows via phpMyAdmin
 * every time. Every row it creates is tagged with a fixed marker in the
 * `remarks` column so a later run can find and clear exactly those rows
 * without touching real data.
 *
 * Usage:
 *   php database/seeders/seed_at_risk_test_data.php [seed|clear] [student_id]
 *
 * With no student_id, picks the most recently added active student in the
 * currently active school year (so it shows up on the At-Risk page's
 * default filter). With no subcommand, defaults to "seed".
 */

if(php_sapi_name() !== 'cli'){
    http_response_code(403);
    exit('This script can only be run from the CLI.');
}

require_once __DIR__ . '/../config/config.php';

const SEED_MARKER = 'SEED_AT_RISK_TEST';

$action = $argv[1] ?? 'seed';
$studentId = isset($argv[2]) ? (int) $argv[2] : null;

if($studentId === null){
    $row = $con->query(
        "SELECT s.id, s.school_year_id, s.first_name, s.last_name
         FROM students s
         JOIN school_year sy ON sy.id = s.school_year_id
         WHERE s.status = 'active' AND sy.status = 'active'
         ORDER BY s.id DESC LIMIT 1"
    )->fetch_assoc();

    if(!$row){
        $row = $con->query("SELECT id, school_year_id, first_name, last_name FROM students WHERE status = 'active' ORDER BY id DESC LIMIT 1")->fetch_assoc();
        if($row){
            echo "Note: no active student found in the currently active school year — using the most recent active student instead. You may need to pick their school year explicitly on the At-Risk page.\n";
        }
    }

    if(!$row){
        exit("No active student found to seed against. Pass a student_id explicitly.\n");
    }

    $studentId = (int) $row['id'];
    $schoolYearId = (int) $row['school_year_id'];
    echo "Target: student #{$studentId} ({$row['first_name']} {$row['last_name']}), school year #{$schoolYearId}\n";
}else{
    $row = $con->query("SELECT school_year_id FROM students WHERE id = {$studentId}")->fetch_assoc();
    if(!$row){
        exit("Student #{$studentId} not found.\n");
    }
    $schoolYearId = (int) $row['school_year_id'];
}

// Always clear this student's previously-seeded rows first so re-running
// (or "clear") never piles up duplicates.
$con->query("DELETE FROM academic_profiles WHERE student_id = {$studentId} AND remarks = '" . SEED_MARKER . "'");
$con->query("DELETE FROM attendance WHERE student_id = {$studentId} AND remarks = '" . SEED_MARKER . "'");
$con->query("DELETE FROM behavioral_profiles WHERE student_id = {$studentId} AND remarks = '" . SEED_MARKER . "'");

if($action === 'clear'){
    echo "Cleared seeded test data for student #{$studentId}.\n";
    exit();
}

$teacher = $con->query("SELECT id FROM users WHERE role = 'teacher' AND status = 'active' LIMIT 1")->fetch_assoc();
if(!$teacher){
    exit("No active teacher found to attribute the seeded records to.\n");
}
$teacherId = (int) $teacher['id'];
$marker = SEED_MARKER;

// One failing subject (below AtRiskModel::FAILING_GRADE = 75)
$stmt = $con->prepare("INSERT INTO academic_profiles (student_id, school_year_id, subject_name, grading_period, grade, remarks, recorded_by) VALUES (?, ?, 'Science', '1st Quarter', 65.00, ?, ?)");
$stmt->bind_param("iisi", $studentId, $schoolYearId, $marker, $teacherId);
$stmt->execute();

// 5 absences (meets AtRiskModel::CHRONIC_ABSENCE_THRESHOLD). attendance has a
// UNIQUE(student_id, attendance_date, session) constraint, so skip any date
// this student already has a Morning record for (real or previously seeded).
$existingDates = [];
$existingResult = $con->query("SELECT attendance_date FROM attendance WHERE student_id = {$studentId} AND session = 'Morning'");
while($existingRow = $existingResult->fetch_assoc()){
    $existingDates[] = $existingRow['attendance_date'];
}

$stmt = $con->prepare("INSERT INTO attendance (student_id, school_year_id, attendance_date, session, status, remarks, recorded_by) VALUES (?, ?, ?, 'Morning', 'Absent', ?, ?)");
$inserted = 0;
$dayOffset = 0;
while($inserted < 5){
    $date = date('Y-m-d', strtotime("-{$dayOffset} days"));
    $dayOffset++;
    if(in_array($date, $existingDates, true)){
        continue;
    }
    $stmt->bind_param("iissi", $studentId, $schoolYearId, $date, $marker, $teacherId);
    $stmt->execute();
    $inserted++;
}

// 3 disciplinary incidents (meets AtRiskModel::DISCIPLINARY_THRESHOLD)
$stmt = $con->prepare("INSERT INTO behavioral_profiles (student_id, school_year_id, observation_date, category, observation, intervention, remarks, recorded_by) VALUES (?, ?, ?, 'Disciplinary', ?, ?, ?, ?)");
for($i = 0; $i < 3; $i++){
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $observation = 'Seeded test observation ' . ($i + 1);
    $intervention = 'Seeded test intervention';
    $stmt->bind_param("iissssi", $studentId, $schoolYearId, $date, $observation, $intervention, $marker, $teacherId);
    $stmt->execute();
}

echo "Seeded at-risk test data for student #{$studentId} (school year #{$schoolYearId}).\n";
echo "Run 'php database/seeders/seed_at_risk_test_data.php clear {$studentId}' to remove it later.\n";
