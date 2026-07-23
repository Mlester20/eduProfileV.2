<?php
/**
 * CLI-only dev tool. Seeds a small but broad set of realistic demo data so
 * there's actually something to show across dashboards, At-Risk Learners,
 * Compiled Records, Learner Profile, and Student Rollover promotion —
 * without hand-typing it all through the UI.
 *
 * Fully idempotent: every insert is guarded by an existence check (by name,
 * email, or LRN), so running this multiple times never creates duplicates.
 *
 * Usage: php database/seeders/seed_demo_data.php
 *
 * Creates, if missing:
 *   - "Grade 2" grade level + a new section ("Neptune") with a brand-new
 *     teacher account (so the rollover-promotion demo has a genuinely
 *     different teacher to promote a student into)
 *   - 4 demo students spread across Grade 1 (existing sections) and the
 *     new Grade 2 section, with a realistic mix of academic, attendance,
 *     behavioral, developmental, health, and achievement records —
 *     including one deliberately at-risk student (failing grade, chronic
 *     absences, repeated disciplinary entries) so At-Risk Learners has
 *     something to flag.
 */

if(php_sapi_name() !== 'cli'){
    http_response_code(403);
    exit('This script can only be run from the CLI.');
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../../app/helpers/password.php';

echo "=== Seeding demo data ===\n";

// --- Active school year -----------------------------------------------
$activeSy = $con->query("SELECT id, school_year FROM school_year WHERE status = 'active' LIMIT 1")->fetch_assoc();
if(!$activeSy){
    exit("No active school year found. Set one first in Manage School Year.\n");
}
$syId = (int) $activeSy['id'];
echo "Using active school year: {$activeSy['school_year']} (id {$syId})\n";

// --- Grade 2 grade level ------------------------------------------------
$grade2 = $con->query("SELECT id FROM grade_levels WHERE grade_name = 'Grade 2'")->fetch_assoc();
if(!$grade2){
    $con->query("INSERT INTO grade_levels (grade_name) VALUES ('Grade 2')");
    $grade2Id = $con->insert_id;
    echo "Created grade level: Grade 2 (id {$grade2Id})\n";
}else{
    $grade2Id = (int) $grade2['id'];
    echo "Grade 2 already exists (id {$grade2Id})\n";
}

// --- New teacher for the Grade 2 section --------------------------------
$teacherEmail = 'teacher2@school.edu.ph';
$teacher = $con->query("SELECT id FROM users WHERE email = '{$teacherEmail}'")->fetch_assoc();
if(!$teacher){
    $hashed = HashPassword::passwordHash('Teacher123!');
    $stmt = $con->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES ('Teacher Two', ?, ?, 'teacher', 'active')");
    $stmt->bind_param("ss", $teacherEmail, $hashed);
    $stmt->execute();
    $teacherId = $con->insert_id;
    echo "Created teacher account: Teacher Two <{$teacherEmail}> / password: Teacher123! (id {$teacherId})\n";
}else{
    $teacherId = (int) $teacher['id'];
    echo "Teacher Two already exists (id {$teacherId})\n";
}

// --- Grade 2 section -----------------------------------------------------
$section = $con->query("SELECT id FROM sections WHERE section_name = 'Neptune'")->fetch_assoc();
if(!$section){
    $stmt = $con->prepare("INSERT INTO sections (grade_level_id, section_name, adviser_id) VALUES (?, 'Neptune', ?)");
    $stmt->bind_param("ii", $grade2Id, $teacherId);
    $stmt->execute();
    $neptuneId = $con->insert_id;
    echo "Created section: Grade 2 - Neptune (id {$neptuneId}, adviser: Teacher Two)\n";
}else{
    $neptuneId = (int) $section['id'];
    echo "Section Neptune already exists (id {$neptuneId})\n";
}

// Existing Grade 1 sections (Mahogani / Venus) — reused as-is.
$mahogani = $con->query("SELECT id, adviser_id FROM sections WHERE section_name = 'Mahogani'")->fetch_assoc();
$venus = $con->query("SELECT id, adviser_id FROM sections WHERE section_name = 'Venus'")->fetch_assoc();
if(!$mahogani || !$venus){
    exit("Expected sections 'Mahogani' and 'Venus' to already exist — run this after the normal app setup.\n");
}

function findOrCreateStudent($con, $lrn, $firstName, $lastName, $birthDate, $gender, $sectionId, $syId, $recordedBy){
    $existing = $con->query("SELECT id FROM students WHERE lrn = '{$lrn}'")->fetch_assoc();
    if($existing){
        echo "  Student {$firstName} {$lastName} already exists (id {$existing['id']}), skipping insert.\n";
        return (int) $existing['id'];
    }
    $stmt = $con->prepare("INSERT INTO students (lrn, first_name, middle_name, last_name, suffix, birth_date, gender, address, school_year_id, grade_level_id, section_id, recorded_by, status) VALUES (?, ?, '', ?, '', ?, ?, 'San Jose Sur, Mallig, Isabela', ?, (SELECT grade_level_id FROM sections WHERE id = ?), ?, ?, 'active')");
    $stmt->bind_param("sssssiiii", $lrn, $firstName, $lastName, $birthDate, $gender, $syId, $sectionId, $sectionId, $recordedBy);
    $stmt->execute();
    $id = $con->insert_id;
    echo "  Created student {$firstName} {$lastName} (id {$id})\n";
    return $id;
}

echo "\n--- Student 1: Juan Dela Cruz (Grade 1 - Mahogani, healthy record) ---\n";
$juanId = findOrCreateStudent($con, '100000000001', 'Juan', 'Dela Cruz', '2019-03-14', 'Male', $mahogani['id'], $syId, $mahogani['adviser_id']);
if($con->query("SELECT id FROM academic_profiles WHERE student_id = {$juanId}")->num_rows === 0){
    $con->query("INSERT INTO academic_profiles (student_id, school_year_id, subject_name, grading_period, grade, remarks, recorded_by) VALUES ({$juanId}, {$syId}, 'English', '1st Quarter', 88.00, 'Passed', {$mahogani['adviser_id']}), ({$juanId}, {$syId}, 'Mathematics', '1st Quarter', 90.00, 'Passed', {$mahogani['adviser_id']})");
    $con->query("INSERT INTO attendance (student_id, school_year_id, attendance_date, session, status, recorded_by) VALUES ({$juanId}, {$syId}, CURDATE(), 'Morning', 'Present', {$mahogani['adviser_id']}), ({$juanId}, {$syId}, CURDATE() - INTERVAL 1 DAY, 'Morning', 'Present', {$mahogani['adviser_id']})");
    $con->query("INSERT INTO behavioral_profiles (student_id, school_year_id, observation_date, category, observation, intervention, remarks, recorded_by) VALUES ({$juanId}, {$syId}, CURDATE(), 'Positive Behavior', 'Helped a classmate with schoolwork.', 'None needed', 'Keep it up', {$mahogani['adviser_id']})");
    $con->query("INSERT INTO achievements_profiles (student_id, school_year_id, title, category, level, description, date_received, awarding_body, recorded_by) VALUES ({$juanId}, {$syId}, 'Reading Champion', 'Academic', 'School', 'Top reader for the quarter', CURDATE(), 'San Jose Sur Elementary', {$mahogani['adviser_id']})");
    $con->query("INSERT IGNORE INTO health_profiles (student_id, school_year_id, height_cm, weight_kg, bmi, bmi_classification, blood_type, allergies, medical_conditions, vision_screening_result, hearing_screening_result, immunization_status, recorded_by) VALUES ({$juanId}, {$syId}, 115.00, 22.00, 16.60, 'Normal', 'O+', 'None', 'None', 'Normal', 'Normal', 'Complete', {$mahogani['adviser_id']})");
    echo "  Added academic/attendance/behavioral/achievement/health records.\n";
}else{
    echo "  Records already exist, skipping.\n";
}

echo "\n--- Student 2: Maria Santos (Grade 1 - Venus, healthy record) ---\n";
$mariaId = findOrCreateStudent($con, '100000000002', 'Maria', 'Santos', '2019-07-22', 'Female', $venus['id'], $syId, $venus['adviser_id']);
if($con->query("SELECT id FROM academic_profiles WHERE student_id = {$mariaId}")->num_rows === 0){
    $con->query("INSERT INTO academic_profiles (student_id, school_year_id, subject_name, grading_period, grade, remarks, recorded_by) VALUES ({$mariaId}, {$syId}, 'Science', '1st Quarter', 85.00, 'Passed', {$venus['adviser_id']}), ({$mariaId}, {$syId}, 'Filipino', '1st Quarter', 82.00, 'Passed', {$venus['adviser_id']})");
    $con->query("INSERT INTO attendance (student_id, school_year_id, attendance_date, session, status, recorded_by) VALUES ({$mariaId}, {$syId}, CURDATE(), 'Morning', 'Present', {$venus['adviser_id']}), ({$mariaId}, {$syId}, CURDATE() - INTERVAL 1 DAY, 'Morning', 'Present', {$venus['adviser_id']})");
    $con->query("INSERT INTO developmental_profiles (student_id, school_year_id, domain, observation, recommendation, recorded_by) VALUES ({$mariaId}, {$syId}, 'Social-Emotional', 'Works well in group activities.', 'Continue encouraging peer collaboration.', {$venus['adviser_id']})");
    echo "  Added academic/attendance/developmental records.\n";
}else{
    echo "  Records already exist, skipping.\n";
}

echo "\n--- Student 3: Pedro Reyes (Grade 1 - Mahogani, AT-RISK) ---\n";
$pedroId = findOrCreateStudent($con, '100000000003', 'Pedro', 'Reyes', '2019-01-30', 'Male', $mahogani['id'], $syId, $mahogani['adviser_id']);
if($con->query("SELECT id FROM academic_profiles WHERE student_id = {$pedroId}")->num_rows === 0){
    $con->query("INSERT INTO academic_profiles (student_id, school_year_id, subject_name, grading_period, grade, remarks, recorded_by) VALUES ({$pedroId}, {$syId}, 'Mathematics', '1st Quarter', 65.00, 'Failed', {$mahogani['adviser_id']})");
    for($i = 0; $i < 5; $i++){
        $con->query("INSERT INTO attendance (student_id, school_year_id, attendance_date, session, status, recorded_by) VALUES ({$pedroId}, {$syId}, CURDATE() - INTERVAL " . (10 + $i) . " DAY, 'Morning', 'Absent', {$mahogani['adviser_id']})");
    }
    for($i = 0; $i < 3; $i++){
        $con->query("INSERT INTO behavioral_profiles (student_id, school_year_id, observation_date, category, observation, intervention, remarks, recorded_by) VALUES ({$pedroId}, {$syId}, CURDATE() - INTERVAL " . (2 + $i) . " DAY, 'Disciplinary', 'Disrupted class activity.', 'Verbal warning given.', 'Monitor closely', {$mahogani['adviser_id']})");
    }
    echo "  Added 1 failing grade, 5 absences, 3 disciplinary entries (crosses all 3 At-Risk thresholds).\n";
}else{
    echo "  Records already exist, skipping.\n";
}

echo "\n--- Student 4: Ana Lopez (Grade 2 - Neptune, new teacher's roster) ---\n";
$anaId = findOrCreateStudent($con, '100000000004', 'Ana', 'Lopez', '2018-11-05', 'Female', $neptuneId, $syId, $teacherId);
if($con->query("SELECT id FROM academic_profiles WHERE student_id = {$anaId}")->num_rows === 0){
    $con->query("INSERT INTO academic_profiles (student_id, school_year_id, subject_name, grading_period, grade, remarks, recorded_by) VALUES ({$anaId}, {$syId}, 'English', '1st Quarter', 91.00, 'Passed', {$teacherId})");
    $con->query("INSERT INTO attendance (student_id, school_year_id, attendance_date, session, status, recorded_by) VALUES ({$anaId}, {$syId}, CURDATE(), 'Morning', 'Present', {$teacherId}), ({$anaId}, {$syId}, CURDATE() - INTERVAL 1 DAY, 'Morning', 'Present', {$teacherId})");
    $con->query("INSERT INTO achievements_profiles (student_id, school_year_id, title, category, level, description, date_received, awarding_body, recorded_by) VALUES ({$anaId}, {$syId}, 'Perfect Attendance', 'Other', 'School', 'No absences for the quarter', CURDATE(), 'San Jose Sur Elementary', {$teacherId})");
    echo "  Added academic/attendance/achievement records.\n";
}else{
    echo "  Records already exist, skipping.\n";
}

echo "\n=== Done. Log in as Teacher Two <{$teacherEmail}> / Teacher123! to see the new Grade 2 - Neptune roster. ===\n";
