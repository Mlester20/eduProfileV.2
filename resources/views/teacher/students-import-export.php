<?php
require_once __DIR__ . '/../../../app/controllers/teacher/StudentsController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

if(isset($_GET['export']) && $_GET['export'] === 'xlsx'){
    require_once __DIR__ . '/../../../app/services/StudentExportService.php';
    StudentExportService::exportXlsx($controller->getAllForExport(), $parent_guardian_by_student);
    exit();
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Import / Export Students</title>
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

    <p class="text-muted">Bring your class roster in from a spreadsheet, or pull your current roster out as one.</p>

    <div class="card mb-4">
        <h5 class="card-header">Export to Excel</h5>
        <div class="card-body">
            <p class="text-muted">Download your full list of active students — including their DepEd SF1 details and parent/guardian info — as an .xlsx file.</p>
            <a href="?export=xlsx" class="btn btn-outline-success">
                <i class="bx bx-download"></i> Export to Excel
            </a>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Import from Excel</h5>
        <div class="card-body">
            <p class="text-muted">Step 1: Download the template and fill in one row per student.</p>
            <a href="../../../app/controllers/teacher/StudentImportController.php?template=1" class="btn btn-outline-secondary mb-4">
                <i class="bx bx-download"></i> Download Template
            </a>

            <p class="text-muted">Step 2: Upload the filled-in template here.</p>
            <form action="../../../app/controllers/teacher/StudentImportController.php" method="post" enctype="multipart/form-data">
                <?= Csrf::field() ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="import_file" class="form-label">Excel File (.xlsx)</label>
                        <input class="form-control" type="file" name="import_file" id="import_file" accept=".xlsx" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="import_school_year_id" class="form-label">School Year</label>
                        <select class="form-select" name="school_year_id" id="import_school_year_id" required>
                            <option value="" selected disabled>-- Choose School Year --</option>
                            <?php foreach (($school_years ?? []) as $school_year): ?>
                                <option value="<?php echo htmlspecialchars($school_year['id']); ?>">
                                    <?php echo htmlspecialchars($school_year['school_year']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Grade Level & Section</label>
                    <p class="form-text text-muted mt-0">All imported students will be assigned to this section.</p>
                    <?php if (count($my_sections ?? []) === 1): ?>
                        <?php $my_section = $my_sections[0]; ?>
                        <input
                            class="form-control"
                            type="text"
                            value="<?php echo htmlspecialchars($my_section['grade_level_name'] . ' - ' . $my_section['section_name']); ?>"
                            disabled
                        >
                        <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($my_section['id']); ?>">
                        <input type="hidden" name="grade_level_id" value="<?php echo htmlspecialchars($my_section['grade_level_id']); ?>">
                    <?php elseif (count($my_sections ?? []) > 1): ?>
                        <select class="form-select" name="section_id" id="import_section_id">
                            <option value="" selected disabled>-- Choose Section --</option>
                            <?php foreach ($my_sections as $section): ?>
                                <option
                                    value="<?php echo htmlspecialchars($section['id']); ?>"
                                    data-grade-level-id="<?php echo htmlspecialchars($section['grade_level_id']); ?>"
                                >
                                    <?php echo htmlspecialchars($section['grade_level_name'] . ' - ' . $section['section_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="grade_level_id" id="import_grade_level_id">
                    <?php else: ?>
                        <input class="form-control" type="text" value="No section assigned to you yet" disabled>
                        <div class="form-text text-danger">Contact an admin to get assigned as a section adviser before importing students.</div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="import_students">
                    Upload & Import
                </button>
            </form>
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
    <script src="../../../public/js/teacher/home.js"></script>
</body>
</html>
