<?php
require_once __DIR__ . '/../../../app/controllers/teacher/classlistcontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

/**
 * Nutritional status badge from bmi_classification. "Not yet recorded"
 * covers students with no height/weight logged under Health Profile yet.
 */
function classListBmiBadge($classification){
    switch($classification){
        case 'Severely Wasted':
        case 'Wasted':
            return ['label' => $classification, 'class' => 'bg-label-danger'];
        case 'Obese':
            return ['label' => 'Obese', 'class' => 'bg-label-danger'];
        case 'Overweight':
            return ['label' => 'Overweight', 'class' => 'bg-label-warning'];
        case 'Normal':
            return ['label' => 'Normal', 'class' => 'bg-label-success'];
        default:
            return ['label' => 'Not yet recorded', 'class' => 'bg-label-secondary'];
    }
}

$classListQueryBase = http_build_query(array_filter([
    'school_year_id' => $school_year_filter,
    'section_id' => $section_filter,
    'search' => $search_filter,
    'per_page' => $per_page,
]));
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Class List</title>
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

    <div class="d-flex justify-content-end mb-2">
        <a
            href="class-list-print.php?school_year_id=<?php echo urlencode((string) ($school_year_filter ?? '')); ?>&section_id=<?php echo urlencode((string) ($section_filter ?? '')); ?>"
            target="_blank"
            class="btn btn-outline-primary"
        >
            <i class="bx bx-printer"></i> Print
        </a>
    </div>

    <form action="class-list.php" method="get" class="d-flex flex-wrap align-items-center gap-2 mb-3">
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
            <label class="form-label mb-0">Section</label>
            <?php if (count($sections ?? []) === 1): ?>
                <?php $my_section = $sections[0]; ?>
                <input class="form-control" type="text" value="<?php echo htmlspecialchars(($my_section['grade_level_name'] ?? '') . ' - ' . $my_section['section_name']); ?>" disabled>
            <?php elseif (count($sections ?? []) > 1): ?>
                <select class="form-select" id="section_id" name="section_id" onchange="this.form.submit()">
                    <option value="">-- All My Sections --</option>
                    <?php foreach($sections as $section): ?>
                        <option value="<?php echo htmlspecialchars($section['id']); ?>" <?php echo ($section_filter !== null && (int) $section_filter === (int) $section['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(($section['grade_level_name'] ?? '') . ' - ' . $section['section_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input class="form-control" type="text" value="No section assigned to you yet" disabled>
            <?php endif; ?>
        </div>
    </form>

    <div class="card mt-4">
        <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>My Class List — Health & Needs Status</span>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select class="form-select form-select-sm" id="class_list_per_page" style="width: auto;">
                    <option value="10" <?php echo (int) $per_page === 10 ? 'selected' : ''; ?>>10 per page</option>
                    <option value="20" <?php echo (int) $per_page === 20 ? 'selected' : ''; ?>>20 per page</option>
                </select>
                <div style="max-width: 280px; width: 100%;">
                    <input
                        type="text"
                        class="form-control"
                        id="class_list_search"
                        placeholder="Search by student name..."
                        autocomplete="off"
                        value="<?php echo htmlspecialchars($search_filter ?? ''); ?>"
                    >
                </div>
            </div>
        </h5>
        <div class="card-body py-2">
            <p class="text-muted mb-0 small">
                Nutritional status comes from each learner's Health Profile BMI classification. "Condition Noted" reflects the Health Profile's Medical Conditions Yes/No answer — it is not an official PWD/SPED determination; verify with the school's SPED coordinator before reporting a learner as PWD or with special needs.
            </p>
        </div>
        <div class="table-responsive nowrap">
            <table class="table" id="class_list_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Grade & Section</th>
                        <th>Nutritional Status</th>
                        <th>Condition Noted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rowOffset = ($classListResult['current_page'] - 1) * $classListResult['per_page']; ?>
                    <?php foreach(($classList ?? []) as $index => $learner): ?>
                        <?php
                            $rowStudentName = trim($learner['student_first_name'] . ' ' . ($learner['student_middle_name'] ?? '') . ' ' . $learner['student_last_name'] . ' ' . ($learner['student_suffix'] ?? ''));
                            $bmiBadge = classListBmiBadge($learner['bmi_classification'] ?? null);
                            $hasCondition = ($learner['has_medical_condition'] ?? 'No') === 'Yes';
                            $conditionText = trim($learner['medical_conditions'] ?? '');
                        ?>
                        <tr>
                            <td><?php echo $rowOffset + $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($rowStudentName); ?></td>
                            <td><?php echo htmlspecialchars(($learner['grade_name'] ?? '') . ' - ' . ($learner['section_name'] ?? '')); ?></td>
                            <td><span class="badge <?php echo $bmiBadge['class']; ?>"><?php echo htmlspecialchars($bmiBadge['label']); ?></span></td>
                            <td>
                                <?php if($hasCondition): ?>
                                    <span class="badge bg-label-warning" title="<?php echo htmlspecialchars($conditionText); ?>">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-label-secondary">No</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($classList)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No students found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(!empty($classList)): ?>
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">
                    Showing <?php echo $rowOffset + 1; ?>–<?php echo $rowOffset + count($classList); ?> of <?php echo (int) $classListResult['total']; ?> learners
                </span>
                <?php if($classListResult['total_pages'] > 1): ?>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?php echo $classListResult['current_page'] <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?<?php echo htmlspecialchars($classListQueryBase); ?>&page=<?php echo $classListResult['current_page'] - 1; ?>">&laquo;</a>
                        </li>
                        <?php for($p = 1; $p <= $classListResult['total_pages']; $p++): ?>
                            <li class="page-item <?php echo $p === $classListResult['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo htmlspecialchars($classListQueryBase); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $classListResult['current_page'] >= $classListResult['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?<?php echo htmlspecialchars($classListQueryBase); ?>&page=<?php echo $classListResult['current_page'] + 1; ?>">&raquo;</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
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
  <script src="../../../public/js/teacher/class-list.js"></script>
</body>
</html>
