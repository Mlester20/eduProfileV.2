<?php
require_once __DIR__ . '/../../../app/controllers/administrative/healthprofilecontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['administrative']);
?>
<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../../public/assets/"
  data-template="vertical-menu-template-free"
>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Health Profile</title>
  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../../../public/assets/img/favicon/logo.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../../public/assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../../../public/assets/css/demo.css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <script src="../../../public/assets/vendor/js/helpers.js"></script>
  <script src="../../../public/assets/js/config.js"></script>
  <style>
    .bmi-badge { font-weight: 600; padding: .25em .6em; border-radius: .375rem; display: inline-block; }
    .bmi-badge-severely-wasted { background-color: #f8d7da; color: #842029; }
    .bmi-badge-wasted          { background-color: #fff3cd; color: #997404; }
    .bmi-badge-normal          { background-color: #d1e7dd; color: #0f5132; }
    .bmi-badge-overweight      { background-color: #fff3cd; color: #997404; }
    .bmi-badge-obese           { background-color: #f8d7da; color: #842029; }
  </style>
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <form action="health-profile.php" method="get" class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <div>
            <label for="school_year_id" class="form-label mb-0">School Year</label>
            <select class="form-select" id="school_year_id" name="school_year_id" onchange="this.form.submit()">
                <option value="">-- All School Years --</option>
                <?php foreach(($school_years ?? []) as $school_year): ?>
                    <option value="<?php echo htmlspecialchars($school_year['id']); ?>" <?php echo ($school_year_filter !== null && (int) $school_year_filter === (int) $school_year['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($school_year['school_year']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="grade_level_id" class="form-label mb-0">Grade Level</label>
            <select class="form-select" id="grade_level_id" name="grade_level_id" onchange="this.form.submit()">
                <option value="">-- All Grade Levels --</option>
                <?php foreach(($grade_levels ?? []) as $grade_level): ?>
                    <option value="<?php echo htmlspecialchars($grade_level['id']); ?>" <?php echo ($grade_level_filter !== null && (int) $grade_level_filter === (int) $grade_level['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($grade_level['grade_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="section_id" class="form-label mb-0">Section</label>
            <select class="form-select" id="section_id" name="section_id" onchange="this.form.submit()">
                <option value="">-- All Sections --</option>
                <?php foreach(($sections ?? []) as $section): ?>
                    <option value="<?php echo htmlspecialchars($section['id']); ?>" <?php echo ($section_filter !== null && (int) $section_filter === (int) $section['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(($section['grade_level_name'] ?? '') . ' - ' . $section['section_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <div class="card mt-4">
        <h5 class="card-header">Health Profile</h5>
        <div class="card-body py-2">
            <p class="text-muted mb-0 small">
                Recorded height, weight, and BMI classification for every learner with a health profile on file, school-wide.
            </p>
        </div>
        <div class="table-responsive nowrap">
            <table class="table">
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
                    <?php foreach(($healthRecords ?? []) as $index => $record): ?>
                        <?php $badgeClass = 'bmi-badge-' . strtolower(str_replace(' ', '-', $record['bmi_classification'] ?? '')); ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars(trim($record['student_first_name'] . ' ' . ($record['student_middle_name'] ?? '') . ' ' . $record['student_last_name'] . ' ' . ($record['student_suffix'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($record['school_year'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(($record['grade_name'] ?? '') . ' - ' . ($record['section_name'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($record['assigned_teacher_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($record['height_cm'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($record['weight_kg'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($record['bmi'] ?? ''); ?></td>
                            <td><span class="bmi-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($record['bmi_classification'] ?? ''); ?></span></td>
                            <td><?php echo htmlspecialchars($record['blood_type'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($healthRecords)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">No health profiles found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
  <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
  <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../../../public/assets/vendor/js/menu.js"></script>
  <script src="../../../public/assets/js/main.js"></script>
</body>
</html>
