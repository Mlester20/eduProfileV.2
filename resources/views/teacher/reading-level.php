<?php
require_once __DIR__ . '/../../../app/controllers/teacher/ReadingLevelController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Reading Level</title>
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

    <?php if (!empty($filtered_student)): ?>
      <?php $filteredName = trim($filtered_student['first_name'] . ' ' . ($filtered_student['middle_name'] ?? '') . ' ' . $filtered_student['last_name']); ?>
      <div class="alert alert-info d-flex justify-content-between align-items-center">
        <span>Showing reading level records for <strong><?= htmlspecialchars($filteredName); ?></strong> only.</span>
        <a href="reading-level.php" class="btn btn-sm btn-outline-secondary">Clear filter</a>
      </div>
    <?php endif; ?>

    <div class="text-end">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReadingLevelModal">Record Reading Level</button>
    </div>

    <!-- add modal -->
    <div class="modal fade" id="createReadingLevelModal" tabindex="-1" aria-labelledby="createReadingLevelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createReadingLevelLabel">Record Reading Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/ReadingLevelController.php" method="post">
                    <?= Csrf::field() ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="student_id_search" class="form-label">Student</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="student_id_search" placeholder="Search student..." autocomplete="off">
                                    <div id="student_id_suggestions" class="list-group shadow-sm position-absolute w-100" style="top: 100%; left: 0; z-index: 5; max-height: 200px; overflow-y: auto; display: none;"></div>
                                </div>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="">Select student</option>
                                    <?php if (!empty($students)): ?>
                                        <?php foreach ($students as $student): ?>
                                            <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']); ?>
                                            <option value="<?= htmlspecialchars($student['id']); ?>"><?= htmlspecialchars($studentName); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">School Year</label>
                                <?php if (!empty($active_sy)): ?>
                                    <input class="form-control" type="text" value="<?= htmlspecialchars($active_sy[0]['school_year']); ?>" disabled>
                                    <input type="hidden" name="school_year_id" id="school_year_id" value="<?= htmlspecialchars($active_sy[0]['id']); ?>">
                                <?php else: ?>
                                    <input class="form-control" type="text" value="No active school year set" disabled>
                                    <div class="form-text text-danger">Contact an admin to set an active school year before recording data.</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="assessment_date" class="form-label">Assessment Date</label>
                                <input type="date" class="form-control" id="assessment_date" name="assessment_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reading_level" class="form-label">Reading Level</label>
                                <select class="form-select" id="reading_level" name="reading_level" required>
                                    <option value="" selected disabled>Select reading level</option>
                                    <option value="Non-reader">Non-reader</option>
                                    <option value="Frustration">Frustration</option>
                                    <option value="Instructional">Instructional</option>
                                    <option value="Independent">Independent</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reading_language" class="form-label">Reading Language</label>
                                <select class="form-select" id="reading_language" name="reading_language" required>
                                    <option value="" selected disabled>Select reading language</option>
                                    <option value="English">English</option>
                                    <option value="Filipino">Filipino</option>
                                    <option value="MTB">MTB</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="e.g., Needs more practice with multisyllabic words."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="create_reading_level">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- update modal -->
    <div class="modal fade" id="editReadingLevelModal" tabindex="-1" aria-labelledby="editReadingLevelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReadingLevelLabel">Record Reading Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/ReadingLevelController.php" method="post">
                    <?= Csrf::field() ?>
                  <input type="hidden" name="id" id="edit_reading_level_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_student_id_search" class="form-label">Student</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="edit_student_id_search" placeholder="Search student..." autocomplete="off">
                                    <div id="edit_student_id_suggestions" class="list-group shadow-sm position-absolute w-100" style="top: 100%; left: 0; z-index: 5; max-height: 200px; overflow-y: auto; display: none;"></div>
                                </div>
                                <select class="form-select" id="edit_student_id" name="student_id" required>
                                    <option value="">Select student</option>
                                    <?php if (!empty($students)): ?>
                                        <?php foreach ($students as $student): ?>
                                            <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']); ?>
                                            <option value="<?= htmlspecialchars($student['id']); ?>"><?= htmlspecialchars($studentName); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">School Year</label>
                                <?php if (!empty($active_sy)): ?>
                                    <input class="form-control" type="text" value="<?= htmlspecialchars($active_sy[0]['school_year']); ?>" disabled>
                                    <input type="hidden" name="school_year_id" id="edit_school_year_id" value="<?= htmlspecialchars($active_sy[0]['id']); ?>">
                                <?php else: ?>
                                    <input class="form-control" type="text" value="No active school year set" disabled>
                                    <div class="form-text text-danger">Contact an admin to set an active school year before recording data.</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_assessment_date" class="form-label">Assessment Date</label>
                                <input type="date" class="form-control" id="edit_assessment_date" name="assessment_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_reading_level" class="form-label">Reading Level</label>
                                <select class="form-select" id="edit_reading_level" name="reading_level" required>
                                    <option value="" selected disabled>Select reading level</option>
                                    <option value="Non-reader">Non-reader</option>
                                    <option value="Frustration">Frustration</option>
                                    <option value="Instructional">Instructional</option>
                                    <option value="Independent">Independent</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_reading_language" class="form-label">Reading Language</label>
                                <select class="form-select" id="edit_reading_language" name="reading_language" required>
                                    <option value="" selected disabled>Select reading language</option>
                                    <option value="English">English</option>
                                    <option value="Filipino">Filipino</option>
                                    <option value="MTB">MTB</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="edit_remarks" name="remarks" rows="2" placeholder="e.g., Needs more practice with multisyllabic words."></textarea>
                            </div>
                        </div>
                        <!-- hide the value of recorded_by to prevent changing the value -->
                        <input type="hidden" name="recorded_by" id="edit_recorded_by">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_reading_level">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- view modal -->
    <div class="modal fade" id="viewReadingLevelModal" tabindex="-1" aria-labelledby="viewReadingLevelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewReadingLevelLabel">Reading Level Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Student</label>
                            <p class="mb-0" id="view_student_name"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">School Year</label>
                            <p class="mb-0" id="view_school_year"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Assessment Date</label>
                            <p class="mb-0" id="view_assessment_date"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Reading Level</label>
                            <p class="mb-0" id="view_reading_level"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Reading Language</label>
                            <p class="mb-0" id="view_reading_language"></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Remarks</label>
                            <p class="mb-0" id="view_remarks"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Recorded By</label>
                            <p class="mb-0" id="view_recorded_by"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
      <h5 class="card-header">Reading Level Records</h5>
      <div class="table-responsive nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Student Name</th>
              <th>Reading Level</th>
              <th>Reading Language</th>
              <th>Assessment Date</th>
              <th>Remarks</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
                $rlRows   = $reading_levels['data']         ?? [];
                $rlPage   = $reading_levels['current_page'] ?? 1;
                $rlPages  = $reading_levels['total_pages']  ?? 1;
                $rlPer    = $reading_levels['per_page']     ?? 10;
                $rlOffset = ($rlPage - 1) * $rlPer;
            ?>
            <?php if(!empty($rlRows)): ?>
              <?php foreach($rlRows as $index => $reading_level): ?>
                <?php $viewStudentName = trim($reading_level['student_first_name'] . ' ' . ($reading_level['student_middle_name'] ?? '') . ' ' . $reading_level['student_last_name'] . ' ' . ($reading_level['student_suffix'] ?? '')); ?>
                <tr
                  role="button"
                  style="cursor: pointer;"
                  data-bs-toggle="modal"
                  data-bs-target="#viewReadingLevelModal"
                  onclick="viewReadingLevel(
                      '<?= htmlspecialchars($viewStudentName) ?>',
                      '<?= htmlspecialchars($reading_level['school_year']) ?>',
                      '<?= htmlspecialchars($reading_level['assessment_date']) ?>',
                      '<?= htmlspecialchars($reading_level['reading_level']) ?>',
                      '<?= htmlspecialchars($reading_level['reading_language']) ?>',
                      '<?= htmlspecialchars($reading_level['remarks']) ?>',
                      '<?= htmlspecialchars($reading_level['recorded_by']) ?>'
                  )"
                >
                  <td><?php echo $rlOffset + $index + 1; ?></td>
                  <td><?php echo htmlspecialchars($reading_level['student_first_name'] . ' ' . $reading_level['student_last_name']); ?></td>
                  <td><?php echo htmlspecialchars($reading_level['reading_level']); ?></td>
                  <td><?php echo htmlspecialchars($reading_level['reading_language']); ?></td>
                  <td><?php echo htmlspecialchars($reading_level['assessment_date']); ?></td>
                  <td><?php echo htmlspecialchars($reading_level['remarks']); ?></td>
                  <td>
                    <button
                      class="btn btn-sm btn-primary"
                      name="update_reading_level_btn"
                      data-bs-toggle="modal"
                      data-bs-target="#editReadingLevelModal"
                      onclick="event.stopPropagation(); editReadingLevel(
                          '<?= htmlspecialchars($reading_level['id']); ?>',
                          '<?= htmlspecialchars($reading_level['student_id']) ?>',
                          '<?= htmlspecialchars($reading_level['school_year_id']) ?>',
                          '<?= htmlspecialchars($reading_level['assessment_date']) ?>',
                          '<?= htmlspecialchars($reading_level['reading_level']) ?>',
                          '<?= htmlspecialchars($reading_level['reading_language']) ?>',
                          '<?= htmlspecialchars($reading_level['remarks']) ?>',
                          '<?= htmlspecialchars($reading_level['recorded_by']) ?>'
                      )"
                    >
                      Edit
                    </button>

                    <form action="../../../app/controllers/teacher/ReadingLevelController.php" method="post" class="d-inline">
                        <?= Csrf::field() ?>
                      <input type="hidden" name="id" value="<?= htmlspecialchars($reading_level['id']); ?>">
                        <button
                          type="submit"
                          name="delete_reading_level"
                          class="btn btn-sm btn-danger"
                          onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this record?');">
                            Delete
                        </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center">No reading level records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php $rlQuery = $filter_student_id !== null ? 'student_id=' . $filter_student_id . '&' : ''; ?>
      <?php if ($rlPages > 1): ?>
      <div class="card-footer">
        <nav>
          <ul class="pagination justify-content-center mb-0">
            <li class="page-item <?php echo $rlPage <= 1 ? 'disabled' : ''; ?>">
              <a class="page-link" href="?<?php echo $rlQuery; ?>page=<?php echo $rlPage - 1; ?>">&laquo;</a>
            </li>
            <?php for ($p = 1; $p <= $rlPages; $p++): ?>
              <li class="page-item <?php echo $p === $rlPage ? 'active' : ''; ?>">
                <a class="page-link" href="?<?php echo $rlQuery; ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $rlPage >= $rlPages ? 'disabled' : ''; ?>">
              <a class="page-link" href="?<?php echo $rlQuery; ?>page=<?php echo $rlPage + 1; ?>">&raquo;</a>
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
  <script src="../../../public/js/teacher/reading-level.js?v=<?php echo filemtime(__DIR__ . '/../../../public/js/teacher/reading-level.js'); ?>"></script>
</body>
</html>
