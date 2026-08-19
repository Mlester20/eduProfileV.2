<?php
require_once __DIR__ . '/../../../app/controllers/administrative/reportscontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['administrative']);

$currentSchoolYearLabel = '';
foreach(($school_years ?? []) as $sy){
    if($school_year_filter !== null && (int) $sy['id'] === (int) $school_year_filter){
        $currentSchoolYearLabel = $sy['school_year'];
        break;
    }
}
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Reports</title>
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
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <form action="reports.php" method="get" class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <div>
            <label for="school_year_id" class="form-label mb-0">School Year</label>
            <select class="form-select" id="school_year_id" name="school_year_id" onchange="this.form.submit()">
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
    </form>

    <div class="card mb-4">
        <h5 class="card-header">
            Section Breakdown <?php echo $currentSchoolYearLabel !== '' ? '&mdash; ' . htmlspecialchars($currentSchoolYearLabel) : ''; ?>
        </h5>
        <p class="text-muted px-4 mb-2 small">Record counts per section for the selected school year, across all categories — sections with low counts relative to their learner count may be behind on recording.</p>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Teacher</th>
                        <th>Active Learners</th>
                        <th>Academic</th>
                        <th>Behavioral</th>
                        <th>Developmental</th>
                        <th>Health</th>
                        <th>Attendance</th>
                        <th>Achievements</th>
                        <th>Reading Level</th>
                        <th>Total Records</th>
                        <th>At-Risk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(($sectionBreakdown ?? []) as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['grade_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['section_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['teacher_name'] ?? '—'); ?></td>
                            <td><?php echo (int) $row['learner_count']; ?></td>
                            <td><?php echo (int) $row['academic_count']; ?></td>
                            <td><?php echo (int) $row['behavioral_count']; ?></td>
                            <td><?php echo (int) $row['developmental_count']; ?></td>
                            <td><?php echo (int) $row['health_count']; ?></td>
                            <td><?php echo (int) $row['attendance_count']; ?></td>
                            <td><?php echo (int) $row['achievements_count']; ?></td>
                            <td><?php echo (int) $row['reading_level_count']; ?></td>
                            <td><strong><?php echo (int) $row['total_records']; ?></strong></td>
                            <td>
                                <?php if((int) $row['at_risk_count'] > 0): ?>
                                    <span class="badge bg-label-danger"><?php echo (int) $row['at_risk_count']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-label-success">0</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($sectionBreakdown)): ?>
                        <tr>
                            <td colspan="13" class="text-center text-muted">No sections found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Recording Activity by School Year</h5>
        <p class="text-muted px-4 mb-2 small">Learner count and total records logged across all categories, per school year — a year with few records relative to its learner count signals delayed reporting.</p>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>School Year</th>
                        <th>Status</th>
                        <th>Learners</th>
                        <th>Total Records</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(($schoolYearComparison ?? []) as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['school_year']); ?></td>
                            <td>
                                <span class="badge bg-label-<?php echo $row['status'] === 'active' ? 'success' : ($row['status'] === 'archived' ? 'secondary' : 'warning'); ?>">
                                    <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo (int) $row['learner_count']; ?></td>
                            <td><?php echo (int) $row['total_records']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($schoolYearComparison)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No school years found.</td>
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
