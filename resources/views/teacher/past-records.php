<?php
require_once __DIR__ . '/../../../app/controllers/teacher/PastRecordsController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Teacher Dashboard</title>
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

    <p class="text-muted">Students who were rolled over to a new school year no longer appear in your active lists — this page keeps their records available for reference.</p>

    <form action="past-records.php" method="get" class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <div>
            <label for="category" class="form-label mb-0">Category</label>
            <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                <?php foreach(PastRecordsController::CATEGORIES as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
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
    </form>

    <div class="card mt-4">
        <h5 class="card-header">Past Records &mdash; <?php echo htmlspecialchars($category); ?></h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>School Year</th>
                        <th>Section</th>
                        <?php if($category === 'Academic'): ?>
                            <th>Subject</th>
                            <th>Grading Period</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        <?php elseif($category === 'Behavioral'): ?>
                            <th>Observation Date</th>
                            <th>Category</th>
                            <th>Observation</th>
                            <th>Intervention</th>
                            <th>Remarks</th>
                        <?php elseif($category === 'Developmental'): ?>
                            <th>Domain</th>
                            <th>Observation</th>
                            <th>Recommendation</th>
                        <?php elseif($category === 'Health'): ?>
                            <th>Height (cm)</th>
                            <th>Weight (kg)</th>
                            <th>BMI</th>
                            <th>BMI Classification</th>
                            <th>Blood Type</th>
                            <th>Allergies</th>
                        <?php elseif($category === 'Attendance'): ?>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        <?php elseif($category === 'Achievements'): ?>
                            <th>Title</th>
                            <th>Level</th>
                            <th>Category</th>
                            <th>Date Received</th>
                            <th>Awarding Body</th>
                        <?php endif; ?>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $pastRows   = $pastRecords['data']         ?? [];
                        $pastPage   = $pastRecords['current_page'] ?? 1;
                        $pastPages  = $pastRecords['total_pages']  ?? 1;
                        $pastPer    = $pastRecords['per_page']     ?? 10;
                        $pastOffset = ($pastPage - 1) * $pastPer;
                        $pastQuery  = 'category=' . urlencode($category) . (($school_year_filter !== null) ? '&school_year_id=' . urlencode($school_year_filter) : '');
                    ?>
                    <?php foreach($pastRows as $index => $record): ?>
                        <tr>
                            <td><?php echo $pastOffset + $index + 1; ?></td>
                            <td><?php echo htmlspecialchars(trim($record['student_first_name'] . ' ' . ($record['student_middle_name'] ?? '') . ' ' . $record['student_last_name'] . ' ' . ($record['student_suffix'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($record['school_year'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(($record['grade_name'] ?? '') . ' - ' . ($record['section_name'] ?? '')); ?></td>
                            <?php if($category === 'Academic'): ?>
                                <td><?php echo htmlspecialchars($record['subject_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['grading_period'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['grade'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></td>
                            <?php elseif($category === 'Behavioral'): ?>
                                <td><?php echo htmlspecialchars($record['observation_date'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['category'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['observation'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['intervention'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></td>
                            <?php elseif($category === 'Developmental'): ?>
                                <td><?php echo htmlspecialchars($record['domain'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['observation'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['recommendation'] ?? ''); ?></td>
                            <?php elseif($category === 'Health'): ?>
                                <td><?php echo htmlspecialchars($record['height_cm'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['weight_kg'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['bmi'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['bmi_classification'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['blood_type'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['allergies'] ?? ''); ?></td>
                            <?php elseif($category === 'Attendance'): ?>
                                <td><?php echo htmlspecialchars($record['attendance_date'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['session'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['status'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></td>
                            <?php elseif($category === 'Achievements'): ?>
                                <td><?php echo htmlspecialchars($record['title'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['level'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['category'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['date_received'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['awarding_body'] ?? ''); ?></td>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($record['recorded_by_name'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($pastRows)): ?>
                        <tr>
                            <td colspan="20" class="text-center text-muted">No past records found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pastPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $pastPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $pastQuery; ?>&page=<?php echo $pastPage - 1; ?>">&laquo;</a>
              </li>
              <?php for ($p = 1; $p <= $pastPages; $p++): ?>
                <li class="page-item <?php echo $p === $pastPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?<?php echo $pastQuery; ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $pastPage >= $pastPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $pastQuery; ?>&page=<?php echo $pastPage + 1; ?>">&raquo;</a>
              </li>
            </ul>
          </nav>
        </div>
        <?php endif; ?>
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
