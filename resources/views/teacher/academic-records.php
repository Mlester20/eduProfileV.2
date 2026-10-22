<?php
require_once __DIR__ . '/../../../app/controllers/teacher/StudentsController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/helpers/Paginator.php';
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
  <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Academic Records</title>
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

    <input type="hidden" id="active_school_year_id" value="<?= htmlspecialchars($school_years[0]['id'] ?? ''); ?>">

    <!-- Academic Records modal: opened per selected student -->
    <div class="modal fade" id="academicRecordsModal" tabindex="-1" aria-labelledby="academicRecordsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="academicRecordsModalLabel">Academic Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ar_student_id">
                    <p class="mb-3">
                        <strong id="ar_student_name"></strong>
                        <span class="text-muted" id="ar_student_meta"></span>
                    </p>

                    <form id="academicRecordForm" class="border rounded p-3 mb-3 bg-light">
                        <input type="hidden" id="ap_edit_id">
                        <div class="row g-3">
                            <div class="col-6 col-md-4 mb-3">
                                <label for="ap_subject_name" class="form-label">Subject Name</label>
                                <select class="form-select" id="ap_subject_name" required>
                                    <option value="" selected disabled>-- Choose Subject --</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label for="ap_grading_period" class="form-label">Grading Period</label>
                                <select class="form-select" id="ap_grading_period" required>
                                    <option value="" selected disabled>-- Choose --</option>
                                    <option value="1st Quarter">1st Quarter</option>
                                    <option value="2nd Quarter">2nd Quarter</option>
                                    <option value="3rd Quarter">3rd Quarter</option>
                                    <option value="4th Quarter">4th Quarter</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2 mb-3">
                                <label for="ap_grade" class="form-label">Grade</label>
                                <input class="form-control" type="number" step="0.01" min="0" max="100" id="ap_grade" required>
                            </div>
                            <div class="col-6 col-md-3 mb-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1" id="ap_submit_btn">Add</button>
                                <button type="button" class="btn btn-outline-secondary d-none" id="ap_cancel_btn">Cancel</button>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-n2 mb-2">Remarks is set automatically from the grade -- 75 and above is Passed, 74 and below is Failed.</small>
                    </form>

                    <div class="d-flex justify-content-end mb-2">
                        <div style="max-width: 220px; width: 100%;">
                            <select class="form-select form-select-sm" id="ap_period_filter">
                                <option value="">All Grading Periods</option>
                                <option value="1st Quarter">1st Quarter</option>
                                <option value="2nd Quarter">2nd Quarter</option>
                                <option value="3rd Quarter">3rd Quarter</option>
                                <option value="4th Quarter">4th Quarter</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Grading Period</th>
                                    <th>Grade</th>
                                    <th>Remarks</th>
                                    <th>Teacher</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="academicRecordList">
                                <tr><td colspan="6" class="text-center text-muted">No academic records yet.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm justify-content-center mb-0" id="academicRecordPagination"></ul>
                    </nav>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Academic Records</h5>
        <div class="position-relative" style="max-width: 320px; width: 100%;">
            <input
                type="text"
                class="form-control"
                id="student_table_search"
                placeholder="Search by name or LRN..."
                autocomplete="off"
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
            >
        </div>
      </div>
      <div class="card-body py-2">
          <p class="text-muted mb-0 small">Pick a student to add or review their recorded grades per subject and grading period.</p>
      </div>
        <div class="table-responsive nowrap">
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>LRN</th>
                <th>Student Name</th>
                <th>Grade Level & Section</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $rows        = $students['data']         ?? [];
                $currentPage = $students['current_page'] ?? 1;
                $totalPages  = $students['total_pages']  ?? 1;
                $perPage     = $students['per_page']     ?? 10;
                $offset      = ($currentPage - 1) * $perPage;
              ?>
              <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $index => $student): ?>
                  <?php $fullName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'] . ' ' . ($student['suffix'] ?? '')); ?>
                  <tr role="button" style="cursor: pointer;" onclick="openAcademicRecordsModal(<?= (int) $student['id'] ?>, '<?= htmlspecialchars(addslashes($fullName)) ?>', '<?= htmlspecialchars(addslashes($student['lrn'] . ' | ' . $student['grade_name'] . ' - ' . $student['section_name'])) ?>', <?= (int) $student['grade_level_id'] ?>)">
                    <td><?php echo $offset + $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($student['lrn']); ?></td>
                    <td><?php echo htmlspecialchars($fullName); ?></td>
                    <td><?php echo htmlspecialchars($student['grade_name'] . ' - ' . $student['section_name']); ?></td>
                    <td>
                      <button
                        class="btn btn-sm btn-primary"
                        type="button"
                        title="Academic Records"
                        onclick="event.stopPropagation(); openAcademicRecordsModal(<?= (int) $student['id'] ?>, '<?= htmlspecialchars(addslashes($fullName)) ?>', '<?= htmlspecialchars(addslashes($student['lrn'] . ' | ' . $student['grade_name'] . ' - ' . $student['section_name'])) ?>', <?= (int) $student['grade_level_id'] ?>)"
                      >
                        <i class="bx bx-book-content"></i> View Records
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center">No students found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php $studentsQuery = isset($_GET['search']) && trim($_GET['search']) !== '' ? 'search=' . urlencode(trim($_GET['search'])) . '&' : ''; ?>
        <?php if ($totalPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $studentsQuery; ?>page=<?php echo $currentPage - 1; ?>">&laquo;</a>
              </li>
              <?php foreach (Paginator::pageWindow($currentPage, $totalPages) as $p): ?>
                <?php if ($p === null): ?>
                  <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                <?php else: ?>
                  <li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo $studentsQuery; ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
              <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $studentsQuery; ?>page=<?php echo $currentPage + 1; ?>">&raquo;</a>
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
    <script src="../../../public/js/teacher/academic-records.js?v=<?php echo filemtime(__DIR__ . '/../../../public/js/teacher/academic-records.js'); ?>"></script>
</body>
</html>
