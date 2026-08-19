<?php
require_once __DIR__ . '/../../../app/controllers/teacher/StudentsController.php';
require_once __DIR__ . '/../../../app/helpers/StudentsAge.php';
require_once __DIR__ . '/../../../app/helpers/SchoolSettings.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

$school = SchoolSettings::get($con);

// Restrict the register to one section at a time (matching how DepEd SF1
// is one register per section) — default to the teacher's only section,
// or the one picked via ?section_id= when they advise more than one.
$printSectionId = isset($_GET['section_id']) && $_GET['section_id'] !== '' ? (int) $_GET['section_id'] : null;
if($printSectionId === null && count($my_sections ?? []) >= 1){
    $printSectionId = (int) $my_sections[0]['id'];
}

$allStudents = $controller->getAllForExport();
$printStudents = array_values(array_filter($allStudents, function($s) use ($printSectionId){
    return (int) $s['section_id'] === $printSectionId;
}));

$maleCount = count(array_filter($printStudents, function($s){ return $s['gender'] === 'Male'; }));
$femaleCount = count(array_filter($printStudents, function($s){ return $s['gender'] === 'Female'; }));

$currentSection = null;
foreach(($my_sections ?? []) as $sec){
    if((int) $sec['id'] === $printSectionId){
        $currentSection = $sec;
        break;
    }
}

$activeSchoolYear = ($school_years ?? [])[0]['school_year'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Print School Register</title>
  <link rel="icon" type="image/x-icon" href="../../../public/assets/img/favicon/logo.png" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/theme-default.css" />
  <link rel="stylesheet" href="../../../public/assets/css/demo.css" />
  <style>
    body { background: #fff; padding: 24px; }
    .sf1-table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .sf1-table th, .sf1-table td { border: 1px solid #444; padding: 3px 4px; text-align: center; vertical-align: middle; }
    .sf1-table th { background: #f2f2f2; font-weight: 600; }
    .sf1-table td.text-left { text-align: left; }
    .sf1-legend th, .sf1-legend td { border: 1px solid #444; padding: 3px 6px; font-size: 10px; text-align: left; vertical-align: top; }
    @media print {
      .no-print { display: none !important; }
      body { padding: 0; }
      @page { size: legal landscape; margin: 10mm; }
    }
  </style>
</head>
<body>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="students.php" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Back to Manage Students
        </a>
        <div class="d-flex gap-2 align-items-center">
            <?php if(count($my_sections ?? []) > 1): ?>
                <form action="students-print.php" method="get" class="d-flex align-items-center gap-2">
                    <label for="section_id" class="form-label mb-0">Section</label>
                    <select class="form-select" name="section_id" id="section_id" onchange="this.form.submit()">
                        <?php foreach($my_sections as $section): ?>
                            <option value="<?php echo htmlspecialchars($section['id']); ?>" <?php echo ((int) $section['id'] === $printSectionId) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($section['grade_level_name'] . ' - ' . $section['section_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bx bx-printer"></i> Print
            </button>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3 mb-2">
        <img src="../../../public/assets/img/favicon/logo.png" alt="School Logo" style="width: 55px; height: 55px;">
        <div>
            <h5 class="mb-0"><?php echo htmlspecialchars($school['school_name']); ?></h5>
            <small class="text-muted">School ID: <?php echo htmlspecialchars($school['school_id']); ?> &nbsp;•&nbsp; <?php echo htmlspecialchars($school['region']); ?> &nbsp;•&nbsp; <?php echo htmlspecialchars($school['division']); ?> &nbsp;•&nbsp; <?php echo htmlspecialchars($school['district']); ?></small>
        </div>
    </div>
    <h4 class="text-center mb-0">School Form 1 (SF1) School Register</h4>
    <p class="text-center text-muted small mb-3">(This replaces Form 1, Master List &amp; STS Form 2-Family Background and Profile)</p>

    <div class="row mb-2">
        <div class="col-md-4"><strong>School Year:</strong> <?php echo htmlspecialchars($activeSchoolYear); ?></div>
        <div class="col-md-4"><strong>Grade Level:</strong> <?php echo htmlspecialchars($currentSection['grade_level_name'] ?? ''); ?></div>
        <div class="col-md-4"><strong>Section:</strong> <?php echo htmlspecialchars($currentSection['section_name'] ?? ''); ?></div>
    </div>

    <div class="table-responsive">
        <table class="sf1-table">
            <thead>
                <tr>
                    <th rowspan="2">LRN</th>
                    <th rowspan="2">Name<br>(Last Name, First Name, Middle Name)</th>
                    <th rowspan="2">Sex</th>
                    <th rowspan="2">Birth Date</th>
                    <th rowspan="2">Age as of<br>1st Friday<br>of June</th>
                    <th rowspan="2">Mother<br>Tongue</th>
                    <th rowspan="2">IP<br>(Ethnic Group)</th>
                    <th rowspan="2">Religion</th>
                    <th colspan="4">Address</th>
                    <th colspan="2">Parents</th>
                    <th colspan="3">Guardian (if not Parent)</th>
                    <th rowspan="2">Learning<br>Modality</th>
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>
                    <th>House #/ Street/ Sitio/ Purok</th>
                    <th>Barangay</th>
                    <th>Municipality/ City</th>
                    <th>Province</th>
                    <th>Father's Name</th>
                    <th>Mother's Name</th>
                    <th>Name</th>
                    <th>Relationship</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($printStudents)): ?>
                    <tr><td colspan="18" class="text-center text-muted">No active students found for this section.</td></tr>
                <?php else: ?>
                    <?php foreach($printStudents as $index => $s): ?>
                        <?php $pg = $parent_guardian_by_student[$s['id']] ?? null; ?>
                        <?php $addressLine = trim(($s['house_number'] ?? '') . ' ' . ($s['street'] ?? '') . ' ' . ($s['sitio'] ?? '') . ' ' . ($s['purok'] ?? '')); ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['lrn'] ?? ''); ?></td>
                            <td class="text-left"><?php echo htmlspecialchars($s['last_name'] . ', ' . $s['first_name'] . ' ' . ($s['middle_name'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars(substr($s['gender'], 0, 1)); ?></td>
                            <td><?php echo htmlspecialchars($s['birth_date'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($s['age_as_of_june'] ?? StudentsAge::calculateAge($s['birth_date'])); ?></td>
                            <td><?php echo htmlspecialchars($s['mother_tongue'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($s['ip_ethnic_group'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($s['religion'] ?? ''); ?></td>
                            <td class="text-left"><?php echo htmlspecialchars($addressLine); ?></td>
                            <td><?php echo htmlspecialchars($s['barangay'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($s['city_municipality'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($s['province'] ?? ''); ?></td>
                            <td class="text-left"><?php echo htmlspecialchars($pg['father_name'] ?? ''); ?></td>
                            <td class="text-left"><?php echo htmlspecialchars($pg['mother_name'] ?? ''); ?></td>
                            <td class="text-left"><?php echo htmlspecialchars($pg['guardian_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($pg['guardian_relationship'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($pg['guardian_contact'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($s['learning_modality'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($s['remarks'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <table class="sf1-table">
                <thead>
                    <tr><th>Registered</th><th>Male</th><th>Female</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>BoSY</td>
                        <td><?php echo $maleCount; ?></td>
                        <td><?php echo $femaleCount; ?></td>
                        <td><?php echo $maleCount + $femaleCount; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <table class="sf1-legend" style="width: 100%;">
            <caption class="text-start fw-bold mb-1" style="caption-side: top;">List and Code of Indicators under REMARKS column</caption>
            <thead>
                <tr><th>Indicator</th><th>Required Information</th></tr>
            </thead>
            <tbody>
                <tr><td>Transferred Out</td><td>Name of Public (P)/Private (PR) School &amp; Effectivity Date</td></tr>
                <tr><td>Transferred In</td><td>Name of Public (P)/Private (PR) School &amp; Effectivity Date</td></tr>
                <tr><td>Dropped</td><td>Reason and Effectivity Date</td></tr>
                <tr><td>Late Enrollment</td><td>Reason (Enrollment beyond 1st Friday of SY)</td></tr>
                <tr><td>CCT Recipient (B/A)</td><td>Indicator</td></tr>
                <tr><td>CCT Control/Reference Number &amp; Effectivity Date</td><td>Code</td></tr>
                <tr><td>SNE (Special Needs Education)</td><td>Specify</td></tr>
                <tr><td>Balik Aral (D)</td><td>Specify</td></tr>
                <tr><td>ACL (Accelerated)</td><td>Specify Level &amp; Effectivity Data</td></tr>
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
            <p class="mb-0" style="border-top: 1px solid #333; width: 80%; padding-top: 4px;"><?php echo !empty($school['school_head']) ? htmlspecialchars($school['school_head']) : '&nbsp;'; ?></p>
            <small class="text-muted">(Signature of School Head over Printed Name)</small>
        </div>
    </div>

    <p class="text-end text-muted small mt-4">Generated on: <?php echo date('l, F j, Y'); ?></p>

</body>
</html>
