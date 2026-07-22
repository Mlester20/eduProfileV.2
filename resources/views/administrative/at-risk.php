<?php
require_once __DIR__ . '/../../../app/controllers/administrative/atriskcontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | At-Risk Learners</title>
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

    <form action="at-risk.php" method="get" class="d-flex flex-wrap align-items-center gap-2 mb-3">
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
        <h5 class="card-header">At-Risk Learners</h5>
        <div class="card-body py-2">
            <p class="text-muted mb-0 small">
                Flagged when a learner has a subject grade below <?php echo AtRiskModel::FAILING_GRADE; ?>,
                <?php echo AtRiskModel::CHRONIC_ABSENCE_THRESHOLD; ?>+ recorded absences, or
                <?php echo AtRiskModel::DISCIPLINARY_THRESHOLD; ?>+ Disciplinary behavioral entries for the selected school year.
            </p>
        </div>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>School Year</th>
                        <th>Section</th>
                        <th>Assigned Teacher</th>
                        <th>Flags</th>
                        <th>AI Insight</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(($atRiskLearners ?? []) as $index => $learner): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars(trim($learner['student_first_name'] . ' ' . ($learner['student_middle_name'] ?? '') . ' ' . $learner['student_last_name'] . ' ' . ($learner['student_suffix'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($learner['school_year'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(($learner['grade_name'] ?? '') . ' - ' . ($learner['section_name'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($learner['assigned_teacher_name'] ?? ''); ?></td>
                            <td>
                                <?php if((int) $learner['failing_count'] >= 1): ?>
                                    <span class="badge bg-label-danger">Failing (<?php echo (int) $learner['failing_count']; ?>)</span>
                                <?php endif; ?>
                                <?php if((int) $learner['absence_count'] >= AtRiskModel::CHRONIC_ABSENCE_THRESHOLD): ?>
                                    <span class="badge bg-label-warning">Chronic Absence (<?php echo (int) $learner['absence_count']; ?>)</span>
                                <?php endif; ?>
                                <?php if((int) $learner['disciplinary_count'] >= AtRiskModel::DISCIPLINARY_THRESHOLD): ?>
                                    <span class="badge bg-label-secondary">Disciplinary (<?php echo (int) $learner['disciplinary_count']; ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td style="min-width: 260px;">
                                <div class="insight-cell" data-student-id="<?php echo (int) $learner['student_id']; ?>" data-school-year-id="<?php echo (int) $learner['school_year_id']; ?>">
                                    <p class="insight-text small mb-1"><?php echo $learner['insight_text'] ? nl2br(htmlspecialchars($learner['insight_text'])) : '<span class="text-muted">No insight generated yet.</span>'; ?></p>
                                    <button type="button" class="btn btn-sm btn-outline-primary generate-insight-btn" onclick="generateInsight(this)">
                                        <?php echo $learner['insight_text'] ? 'Regenerate' : 'Generate Insight'; ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($atRiskLearners)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No learners currently flagged for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?= Csrf::field() ?>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
  <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
  <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../../../public/assets/vendor/js/menu.js"></script>
  <script src="../../../public/assets/js/main.js"></script>
  <script>
    function generateInsight(button){
        const cell = button.closest('.insight-cell');
        const studentId = cell.dataset.studentId;
        const schoolYearId = cell.dataset.schoolYearId;
        const textEl = cell.querySelector('.insight-text');
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;

        button.disabled = true;
        const originalLabel = button.textContent;
        button.textContent = 'Generating...';

        const params = new URLSearchParams();
        params.set('student_id', studentId);
        params.set('school_year_id', schoolYearId);
        params.set('csrf_token', csrfToken);

        fetch('../../../app/controllers/administrative/insightcontroller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                textEl.textContent = data.insight;
                button.textContent = 'Regenerate';
            }else{
                alert(data.message || 'Failed to generate insight.');
                button.textContent = originalLabel;
            }
        })
        .catch(() => {
            alert('Network error while generating insight.');
            button.textContent = originalLabel;
        })
        .finally(() => {
            button.disabled = false;
        });
    }
  </script>
</body>
</html>
