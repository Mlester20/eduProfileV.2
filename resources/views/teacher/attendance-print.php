<?php
session_start();
require_once __DIR__ . '/../../../app/controllers/teacher/AttendanceController.php';
require_once __DIR__ . '/../../../app/helpers/SchoolSettings.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

$controller = new AttendanceController($con);
$school = SchoolSettings::get($con);
$my_sections = $controller->getMySections();
$activeSchoolYear = $controller->getActiveSchoolYear();

// Section to print — default to the teacher's only/first section, matching
// the same picker pattern already used in students-print.php.
$printSectionId = isset($_GET['section_id']) && $_GET['section_id'] !== '' ? (int) $_GET['section_id'] : null;
if($printSectionId === null && count($my_sections ?? []) >= 1){
    $printSectionId = (int) $my_sections[0]['id'];
}
$currentSection = null;
foreach(($my_sections ?? []) as $sec){
    if((int) $sec['id'] === $printSectionId){
        $currentSection = $sec;
        break;
    }
}

// Month to print — ?ym=YYYY-MM, defaults to the current month.
$ym = isset($_GET['ym']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ym']) ? $_GET['ym'] : date('Y-m');
[$printYear, $printMonth] = array_map('intval', explode('-', $ym));
$daysInMonth = (int) date('t', mktime(0, 0, 0, $printMonth, 1, $printYear));

// School days only (Mon-Fri) — SF2 doesn't include weekends as columns.
$schoolDays = [];
for($d = 1; $d <= $daysInMonth; $d++){
    $date = sprintf('%04d-%02d-%02d', $printYear, $printMonth, $d);
    $dayOfWeek = (int) date('N', mktime(0, 0, 0, $printMonth, $d, $printYear));
    if($dayOfWeek <= 5){
        $schoolDays[] = ['date' => $date, 'day' => $d];
    }
}
$monthLabel = date('F Y', mktime(0, 0, 0, $printMonth, 1, $printYear));

$gridStudents = [];
$attendanceGrid = [];
if($printSectionId !== null && !empty($schoolDays)){
    $gridData = $controller->monthlyGrid($printSectionId, $schoolDays[0]['date'], end($schoolDays)['date']);
    $gridStudents = $gridData['students'];
    foreach($gridData['records'] as $r){
        $attendanceGrid[$r['student_id']][$r['attendance_date']][$r['session']] = $r['status'];
    }
}

$statusCodes = ['Present' => 'P', 'Absent' => 'A', 'Late' => 'L', 'Excused' => 'E'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Print Attendance</title>
  <link rel="icon" type="image/x-icon" href="../../../public/assets/img/favicon/logo.png" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/theme-default.css" />
  <link rel="stylesheet" href="../../../public/assets/css/demo.css" />
  <style>
    body { background: #fff; padding: 24px; }
    .sf2-table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .sf2-table th, .sf2-table td { border: 1px solid #444; padding: 3px 4px; text-align: center; vertical-align: middle; }
    .sf2-table th { background: #f2f2f2; font-weight: 600; }
    .sf2-table td.text-left { text-align: left; white-space: nowrap; }
    .sf2-legend th, .sf2-legend td { border: 1px solid #444; padding: 3px 6px; font-size: 10px; text-align: left; vertical-align: top; }
    @media print {
      .no-print { display: none !important; }
      body { padding: 0; }
      .sf2-table { font-size: 7px; }
      .sf2-table th, .sf2-table td { padding: 2px; }
      @page { size: legal landscape; margin: 8mm; }
    }
  </style>
</head>
<body>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="attendance.php" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Back to Attendance
        </a>
        <form action="attendance-print.php" method="get" class="d-flex align-items-center gap-2">
            <?php if(count($my_sections ?? []) > 1): ?>
                <label for="section_id" class="form-label mb-0">Section</label>
                <select class="form-select" name="section_id" id="section_id" onchange="this.form.submit()">
                    <?php foreach($my_sections as $section): ?>
                        <option value="<?php echo htmlspecialchars($section['id']); ?>" <?php echo ((int) $section['id'] === $printSectionId) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($section['grade_level_name'] . ' - ' . $section['section_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($printSectionId ?? ''); ?>">
            <?php endif; ?>
            <label for="ym" class="form-label mb-0">Month</label>
            <input type="month" class="form-control" name="ym" id="ym" value="<?php echo htmlspecialchars($ym); ?>" onchange="this.form.submit()">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bx bx-printer"></i> Print
            </button>
        </form>
    </div>

    <div class="d-flex align-items-center gap-3 mb-2">
        <img src="../../../public/assets/img/favicon/logo.png" alt="School Logo" style="width: 55px; height: 55px;">
        <div>
            <h5 class="mb-0"><?php echo htmlspecialchars($school['school_name']); ?></h5>
            <small class="text-muted">School ID: <?php echo htmlspecialchars($school['school_id']); ?> &nbsp;•&nbsp; <?php echo htmlspecialchars($school['region']); ?> &nbsp;•&nbsp; <?php echo htmlspecialchars($school['division']); ?> &nbsp;•&nbsp; <?php echo htmlspecialchars($school['district']); ?></small>
        </div>
    </div>
    <h4 class="text-center mb-0">School Form 2 (SF2) Daily Attendance Report of Learners</h4>
    <p class="text-center text-muted small mb-3">Month of <?php echo htmlspecialchars($monthLabel); ?></p>

    <div class="row mb-2">
        <div class="col-md-4"><strong>School Year:</strong> <?php echo htmlspecialchars($activeSchoolYear['school_year'] ?? ''); ?></div>
        <div class="col-md-4"><strong>Grade Level:</strong> <?php echo htmlspecialchars($currentSection['grade_level_name'] ?? ''); ?></div>
        <div class="col-md-4"><strong>Section:</strong> <?php echo htmlspecialchars($currentSection['section_name'] ?? ''); ?></div>
    </div>

    <div class="table-responsive">
        <table class="sf2-table">
            <thead>
                <tr>
                    <th rowspan="2" style="min-width: 140px;">Learner's Name</th>
                    <?php foreach($schoolDays as $sd): ?>
                        <th colspan="2">
                            <?php echo $sd['day']; ?><br>
                            <small class="fw-normal"><?php echo htmlspecialchars(date('D', strtotime($sd['date']))); ?></small>
                        </th>
                    <?php endforeach; ?>
                    <th rowspan="2">Present</th>
                    <th rowspan="2">Absent</th>
                </tr>
                <tr>
                    <?php foreach($schoolDays as $sd): ?>
                        <th>AM</th>
                        <th>PM</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($gridStudents)): ?>
                    <tr><td colspan="<?php echo (count($schoolDays) * 2) + 3; ?>" class="text-center text-muted">No active students found for this section.</td></tr>
                <?php else: ?>
                    <?php foreach($gridStudents as $s): ?>
                        <?php
                            $presentCount = 0;
                            $absentCount = 0;
                            $rowCells = [];
                            foreach($schoolDays as $sd){
                                $cell = $attendanceGrid[$s['student_id']][$sd['date']] ?? [];
                                $amCode = $statusCodes[$cell['Morning'] ?? null] ?? '';
                                $pmCode = $statusCodes[$cell['Afternoon'] ?? null] ?? '';
                                $rowCells[] = [$amCode, $pmCode];
                                if($amCode === 'P') $presentCount++;
                                if($pmCode === 'P') $presentCount++;
                                if($amCode === 'A') $absentCount++;
                                if($pmCode === 'A') $absentCount++;
                            }
                        ?>
                        <tr>
                            <td class="text-left"><?php echo htmlspecialchars($s['student_last_name'] . ', ' . $s['student_first_name'] . ' ' . ($s['student_middle_name'] ?? '')); ?></td>
                            <?php foreach($rowCells as [$amCode, $pmCode]): ?>
                                <td><?php echo htmlspecialchars($amCode); ?></td>
                                <td><?php echo htmlspecialchars($pmCode); ?></td>
                            <?php endforeach; ?>
                            <td><?php echo $presentCount; ?></td>
                            <td><?php echo $absentCount; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="row mt-4">
        <table class="sf2-legend" style="width: 100%; max-width: 400px;">
            <caption class="text-start fw-bold mb-1" style="caption-side: top;">Legend</caption>
            <tbody>
                <tr><td><strong>P</strong></td><td>Present</td></tr>
                <tr><td><strong>A</strong></td><td>Absent</td></tr>
                <tr><td><strong>L</strong></td><td>Late</td></tr>
                <tr><td><strong>E</strong></td><td>Excused</td></tr>
                <tr><td>(blank)</td><td>Not yet recorded for that AM/PM session</td></tr>
            </tbody>
        </table>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <p class="mb-0">Prepared by:</p>
            <p class="mb-0" style="border-top: 1px solid #333; width: 80%; padding-top: 4px;">
                <?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>
            </p>
            <small class="text-muted">(Signature of Adviser over Printed Name)</small>
        </div>
        <div class="col-md-6">
            <p class="mb-0">Certified Correct:</p>
            <p class="mb-0" style="border-top: 1px solid #333; width: 80%; padding-top: 4px;">&nbsp;</p>
            <small class="text-muted">(Signature of School Head over Printed Name)</small>
        </div>
    </div>

    <p class="text-end text-muted small mt-4">Generated on: <?php echo date('l, F j, Y'); ?></p>

</body>
</html>
