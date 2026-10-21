<?php
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/controllers/administrative/healthprofilecontroller.php';
require_once __DIR__ . '/../../../app/helpers/SchoolSettings.php';

$school = SchoolSettings::get($con);

// Filter recap for the print header — mirrors health-profile.php's own
// resolution of the selected school year / grade level / section labels.
$schoolYearLabel = 'All School Years';
foreach(($school_years ?? []) as $sy){
    if($school_year_filter !== null && (int) $sy['id'] === (int) $school_year_filter){
        $schoolYearLabel = $sy['school_year'];
        break;
    }
}
$gradeLevelLabel = 'All Grade Levels';
foreach(($grade_levels ?? []) as $gl){
    if($grade_level_filter !== null && (int) $gl['id'] === (int) $grade_level_filter){
        $gradeLevelLabel = $gl['grade_name'];
        break;
    }
}
$sectionLabel = 'All Sections';
foreach(($sections ?? []) as $sec){
    if($section_filter !== null && (int) $sec['id'] === (int) $section_filter){
        $sectionLabel = trim(($sec['grade_level_name'] ?? '') . ' - ' . ($sec['section_name'] ?? ''));
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Print Health Profile</title>
  <link rel="icon" type="image/x-icon" href="../../../public/assets/img/favicon/logo.png" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/theme-default.css" />
  <link rel="stylesheet" href="../../../public/assets/css/demo.css" />
  <style>
    body { background: #fff; padding: 24px; }
    .hp-table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .hp-table th, .hp-table td { border: 1px solid #444; padding: 4px 6px; text-align: left; vertical-align: middle; }
    .hp-table th { background: #f2f2f2; font-weight: 600; }
    @media print {
      .no-print { display: none !important; }
      body { padding: 0; }
      @page { size: legal landscape; margin: 10mm; }
    }
  </style>
</head>
<body>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="health-profile.php" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Back to Health Profile
        </a>
        <form action="health-profile-print.php" method="get" class="d-flex align-items-center gap-2">
            <select class="form-select" name="school_year_id" onchange="this.form.submit()">
                <option value="">-- All School Years --</option>
                <?php foreach(($school_years ?? []) as $sy): ?>
                    <option value="<?php echo htmlspecialchars($sy['id']); ?>" <?php echo ($school_year_filter !== null && (int) $school_year_filter === (int) $sy['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sy['school_year']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select class="form-select" name="grade_level_id" onchange="this.form.submit()">
                <option value="">-- All Grade Levels --</option>
                <?php foreach(($grade_levels ?? []) as $gl): ?>
                    <option value="<?php echo htmlspecialchars($gl['id']); ?>" <?php echo ($grade_level_filter !== null && (int) $grade_level_filter === (int) $gl['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($gl['grade_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select class="form-select" name="section_id" onchange="this.form.submit()">
                <option value="">-- All Sections --</option>
                <?php foreach(($sections ?? []) as $sec): ?>
                    <option value="<?php echo htmlspecialchars($sec['id']); ?>" <?php echo ($section_filter !== null && (int) $section_filter === (int) $sec['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(($sec['grade_level_name'] ?? '') . ' - ' . $sec['section_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
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
    <h4 class="text-center mb-0">Health Profile Report</h4>
    <p class="text-center text-muted small mb-3">
        School Year: <?php echo htmlspecialchars($schoolYearLabel); ?> &nbsp;•&nbsp; Grade Level: <?php echo htmlspecialchars($gradeLevelLabel); ?> &nbsp;•&nbsp; Section: <?php echo htmlspecialchars($sectionLabel); ?>
    </p>

    <div class="table-responsive">
        <table class="hp-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>School Year</th>
                    <th>Grade & Section</th>
                    <th>Assigned Teacher</th>
                    <th>Height (cm)</th>
                    <th>Weight (kg)</th>
                    <th>BMI</th>
                    <th>Classification</th>
                    <th>Blood Type</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($healthRecords)): ?>
                    <tr><td colspan="10" class="text-center text-muted">No health profiles found for the selected filters.</td></tr>
                <?php else: ?>
                    <?php foreach($healthRecords as $index => $record): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars(trim($record['student_first_name'] . ' ' . ($record['student_middle_name'] ?? '') . ' ' . $record['student_last_name'] . ' ' . ($record['student_suffix'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($record['school_year'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(($record['grade_name'] ?? '') . ' - ' . ($record['section_name'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($record['assigned_teacher_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($record['height_cm'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($record['weight_kg'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($record['bmi'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($record['bmi_classification'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($record['blood_type'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <p class="mb-0">Compiled by:</p>
            <p class="mb-0" style="border-top: 1px solid #333; width: 80%; padding-top: 4px;">
                <?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>
            </p>
            <small class="text-muted">(Signature over Printed Name — Administrative)</small>
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
