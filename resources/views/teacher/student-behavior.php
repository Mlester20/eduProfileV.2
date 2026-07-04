<?php
require_once __DIR__ . '/../../../app/controllers/teacher/StudentBehaviorProfileController.php';
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Student Behavior Profiles</title>
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

    <div class="text-end">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentBehavioralModal">Record Student Behavior</button>
    </div>

    <!-- add modal -->
    <div class="modal fade" id="createStudentBehavioralModal" tabindex="-1" aria-labelledby="createStudentBehavioralLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStudentBehavioralLabel">Record Student Behavior</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/StudentBehaviorProfileController.php" method="post">
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
                                <label for="school_year_id" class="form-label">School Year</label>
                                <select class="form-select" id="school_year_id" name="school_year_id" required>
                                    <option value="">Select school year</option>
                                    <?php if (!empty($active_sy)): ?>
                                        <?php foreach ($active_sy as $sy): ?>
                                            <option value="<?= htmlspecialchars($sy['id']); ?>"><?= htmlspecialchars($sy['school_year']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="observation_date" class="form-label">Observation Date</label>
                                <input type="date" class="form-control" id="observation_date" name="observation_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="observation" class="form-label">Observation</label>
                                <textarea class="form-control" id="observation" name="observation" rows="3" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="intervention" class="form-label">Intervention</label>
                                <textarea class="form-control" id="intervention" name="intervention" rows="3" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="create_student_behavioral">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- update modal -->
    <div class="modal fade" id="editStudentBehavioralModal" tabindex="-1" aria-labelledby="editStudentBehavioralLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentBehavioralLabel">Record Student Behavior</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/StudentBehaviorProfileController.php" method="post">
                  <input type="hidden" name="id" id="edit_student_behavioral_id">
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
                                <label for="edit_school_year_id" class="form-label">School Year</label>
                                <select class="form-select" id="edit_school_year_id" name="school_year_id" required>
                                    <option value="">Select school year</option>
                                    <?php if (!empty($active_sy)): ?>
                                        <?php foreach ($active_sy as $sy): ?>
                                            <option value="<?= htmlspecialchars($sy['id']); ?>"><?= htmlspecialchars($sy['school_year']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_observation_date" class="form-label">Observation Date</label>
                                <input type="date" class="form-control" id="edit_observation_date" name="observation_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="edit_category" name="category" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_observation" class="form-label">Observation</label>
                                <textarea class="form-control" id="edit_observation" name="observation" rows="3" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_intervention" class="form-label">Intervention</label>
                                <textarea class="form-control" id="edit_intervention" name="intervention" rows="3" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="edit_remarks" name="remarks" rows="2"></textarea>
                            </div>
                        </div>
                        <!-- hide the value of recorded_by to prevent changing the value -->
                        <input type="hidden" name="recorded_by" id="edit_recorded_by">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_student_behavioral">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- view modal -->
    <div class="modal fade" id="viewStudentBehavioralModal" tabindex="-1" aria-labelledby="viewStudentBehavioralLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentBehavioralLabel">Student Behavior Details</h5>
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
                            <label class="form-label fw-bold">Observation Date</label>
                            <p class="mb-0" id="view_observation_date"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <p class="mb-0" id="view_category"></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Observation</label>
                            <p class="mb-0" id="view_observation"></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Intervention</label>
                            <p class="mb-0" id="view_intervention"></p>
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
      <h5 class="card-header">Student Behavior Profiles</h5>
      <div class="table-responsive nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Student Name</th>
              <th>Observation Date</th>
              <th>Category</th>
              <th>Observation</th>
              <th>Intervention</th>
              <th>Remarks</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($student_behavioral_profiles)): ?>
              <?php foreach($student_behavioral_profiles as $index => $student_behavioral_profile): ?>
                <?php $viewStudentName = trim($student_behavioral_profile['student_first_name'] . ' ' . ($student_behavioral_profile['student_middle_name'] ?? '') . ' ' . $student_behavioral_profile['student_last_name'] . ' ' . ($student_behavioral_profile['student_suffix'] ?? '')); ?>
                <tr
                  role="button"
                  style="cursor: pointer;"
                  data-bs-toggle="modal"
                  data-bs-target="#viewStudentBehavioralModal"
                  onclick="viewStudentBehavioral(
                      '<?= htmlspecialchars($viewStudentName) ?>',
                      '<?= htmlspecialchars($student_behavioral_profile['school_year']) ?>',
                      '<?= htmlspecialchars($student_behavioral_profile['observation_date']) ?>',
                      '<?= htmlspecialchars($student_behavioral_profile['category']) ?>',
                      '<?= htmlspecialchars($student_behavioral_profile['observation']) ?>',
                      '<?= htmlspecialchars($student_behavioral_profile['intervention']) ?>',
                      '<?= htmlspecialchars($student_behavioral_profile['remarks']) ?>',
                      '<?= htmlspecialchars($student_behavioral_profile['recorded_by']) ?>'
                  )"
                >
                  <td><?php echo $index + 1; ?></td>
                  <td><?php echo htmlspecialchars($student_behavioral_profile['student_first_name'] . ' ' . $student_behavioral_profile['student_last_name']); ?></td>
                  <td><?php echo htmlspecialchars($student_behavioral_profile['observation_date']); ?></td>
                  <td><?php echo htmlspecialchars($student_behavioral_profile['category']); ?></td>
                  <td><?php echo htmlspecialchars($student_behavioral_profile['observation']); ?></td>
                  <td><?php echo htmlspecialchars($student_behavioral_profile['intervention']); ?></td>
                  <td><?php echo htmlspecialchars($student_behavioral_profile['remarks']); ?></td>
                  <td>
                    <button
                      class="btn btn-sm btn-primary"
                      name="update_student_behavior"
                      data-bs-toggle="modal"
                      data-bs-target="#editStudentBehavioralModal"
                      onclick="event.stopPropagation(); editStudentBehavioral(
                          '<?= htmlspecialchars($student_behavioral_profile['id']); ?>',
                          '<?= htmlspecialchars($student_behavioral_profile['student_id']) ?>',
                          '<?= htmlspecialchars($student_behavioral_profile['school_year_id']) ?>',
                          '<?= htmlspecialchars($student_behavioral_profile['observation_date']) ?>',
                          '<?= htmlspecialchars($student_behavioral_profile['category']) ?>',
                          '<?= htmlspecialchars($student_behavioral_profile['observation']) ?>',
                          '<?= htmlspecialchars($student_behavioral_profile['intervention']) ?>',
                          '<?= htmlspecialchars($student_behavioral_profile['remarks']) ?>',
                          '<?= htmlspecialchars($student_behavioral_profile['recorded_by']) ?>',
                      )"
                    >
                      Edit
                    </button>

                    <form action="../../../app/controllers/teacher/StudentBehaviorProfileController.php" method="post" class="d-inline">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($student_behavioral_profile['id']); ?>">
                        <button
                          type="submit"
                          name="delete_behavior_profile"
                          class="btn btn-sm btn-danger"
                          onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this record?');">
                            Delete
                        </button>
                    </form>
                  </td>
                </tr>
          </tbody>
          <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">No student behavioral profiles found.</td>
            </tr>
          <?php endif; ?>
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
  <script src="../../../public/js/teacher/student-behavioral.js"></script>
</body>
</html>
