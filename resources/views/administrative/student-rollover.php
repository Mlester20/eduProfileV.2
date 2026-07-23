<?php
require_once __DIR__ . '/../../../app/controllers/administrative/studentrollovercontroller.php';
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Administrative Dashboard</title>
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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="student-rollover.php" method="get" class="d-flex align-items-center gap-2">
            <label for="filter_school_year_id" class="form-label mb-0">From School Year</label>
            <select class="form-select" id="filter_school_year_id" name="school_year_id" onchange="this.form.submit()">
                <option value="" <?php echo $school_year_filter === null ? 'selected' : ''; ?>>-- Choose School Year --</option>
                <?php foreach(($school_years ?? []) as $school_year): ?>
                    <option value="<?php echo htmlspecialchars($school_year['id']); ?>" <?php echo ($school_year_filter !== null && (int) $school_year_filter === (int) $school_year['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($school_year['school_year']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">Roll Over Students to a New School Year</h5>
        <div class="card-body">
            <?php if($school_year_filter === null): ?>
                <p class="text-muted mb-0">Choose a school year above to see its active students.</p>
            <?php elseif(empty($rolloverCandidates)): ?>
                <p class="text-muted mb-0">No active students found for the selected school year.</p>
            <?php else: ?>
                <form action="../../../app/controllers/administrative/studentrollovercontroller.php" method="post" id="rolloverForm">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="rollover_students" value="1">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="new_school_year_id" class="form-label">Roll Over To</label>
                            <select class="form-select" id="new_school_year_id" name="new_school_year_id" required>
                                <option value="" selected disabled>-- Choose Target School Year --</option>
                                <?php foreach(($school_years ?? []) as $school_year): ?>
                                    <?php if((int) $school_year['id'] === (int) $school_year_filter) continue; ?>
                                    <option value="<?php echo htmlspecialchars($school_year['id']); ?>" data-status="<?php echo htmlspecialchars($school_year['status']); ?>">
                                        <?php echo htmlspecialchars($school_year['school_year']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Rolling over to a school year that isn't already <strong>Active</strong> will automatically make it the new active school year for the whole system.</div>
                        </div>
                    </div>

                    <div class="table-responsive nowrap mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAllStudents"></th>
                                    <th>LRN</th>
                                    <th>Student Name</th>
                                    <th>Current Grade & Section</th>
                                    <th>Promote To</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rolloverCandidates as $student): ?>
                                    <tr>
                                        <td><input type="checkbox" class="rollover-student-checkbox" name="student_ids[]" value="<?php echo htmlspecialchars($student['id']); ?>"></td>
                                        <td><?php echo htmlspecialchars($student['lrn']); ?></td>
                                        <td><?php echo htmlspecialchars(trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'] . ' ' . ($student['suffix'] ?? ''))); ?></td>
                                        <td><?php echo htmlspecialchars(($student['grade_name'] ?? '') . ' - ' . ($student['section_name'] ?? '')); ?></td>
                                        <td>
                                            <select class="form-select form-select-sm" name="target_section_id[<?php echo htmlspecialchars($student['id']); ?>]">
                                                <option value="">-- Keep Current Section --</option>
                                                <?php foreach(($all_sections ?? []) as $section): ?>
                                                    <option value="<?php echo htmlspecialchars($section['id']); ?>">
                                                        <?php echo htmlspecialchars(($section['grade_level_name'] ?? '') . ' - ' . $section['section_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-primary" id="rolloverSubmitBtn">
                        <i class="bx bx-transfer"></i> Roll Over Selected Students
                    </button>
                </form>
            <?php endif; ?>
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
  <script>
    document.getElementById('selectAllStudents')?.addEventListener('change', function(){
        document.querySelectorAll('.rollover-student-checkbox').forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('rolloverForm')?.addEventListener('submit', function(e){
        e.preventDefault();
        const form = this;
        const select = document.getElementById('new_school_year_id');
        const selectedOption = select.options[select.selectedIndex];
        const status = selectedOption ? selectedOption.dataset.status : null;

        let options;
        if(status && status !== 'active'){
            options = {
                icon: 'warning',
                title: 'Set as new active school year?',
                html: '<strong>' + selectedOption.textContent.trim() + '</strong> is not the currently active school year.<br><br>' +
                    'Rolling over will automatically set it as the new <strong>active</strong> school year (and deactivate the current one), ' +
                    'even if the current school year period hasn\'t ended yet. Teachers will immediately see it as their working year.',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            };
        }else{
            options = {
                icon: 'question',
                title: 'Roll over selected students?',
                text: 'This will roll over the selected students to the chosen school year.',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            };
        }

        Swal.fire(options).then((result) => {
            if(result.isConfirmed){
                form.submit();
            }
        });
    });
  </script>
</body>
</html>
