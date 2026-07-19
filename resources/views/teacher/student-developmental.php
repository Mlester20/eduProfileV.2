<?php
require_once __DIR__ . '/../../../app/controllers/teacher/DevelopmentalProfileController.php';
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

    <?php if (!empty($filtered_student)): ?>
      <?php $filteredName = trim($filtered_student['first_name'] . ' ' . ($filtered_student['middle_name'] ?? '') . ' ' . $filtered_student['last_name']); ?>
      <div class="alert alert-info d-flex justify-content-between align-items-center">
        <span>Showing developmental records for <strong><?= htmlspecialchars($filteredName); ?></strong> only.</span>
        <a href="student-developmental.php" class="btn btn-sm btn-outline-secondary">Clear filter</a>
      </div>
    <?php endif; ?>

    <div class="text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentDevelopmentalModal">Record Student Developmental</button>
    </div>

    <div class="modal fade" id="createStudentDevelopmentalModal" tabindex="-1" aria-labelledby="createStudentDevelopmentalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStudentDevelopmentalLabel">Record Student Developmental Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/DevelopmentalProfileController.php" method="post">
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
                                <label for="domain" class="form-label">Domain</label>
                                <select class="form-select" id="domain" name="domain" required>
                                    <option value="">Select domain</option>
                                    <option value="Cognitive">Cognitive</option>
                                    <option value="Social">Social</option>
                                    <option value="Emotional">Emotional</option>
                                    <option value="Physical">Physical</option>
                                    <option value="Language">Language</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="observation" class="form-label">Observation</label>
                                <textarea class="form-control" id="observation" name="observation" rows="3" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="recommendation" class="form-label">Recommendation</label>
                                <textarea class="form-control" id="recommendation" name="recommendation" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button
                            type="submit"
                            class="btn btn-primary" name="create_developmental_profile"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- update modal -->
    <div class="modal fade" id="updateStudentDevelopmentalModal" tabindex="-1" aria-labelledby="updateStudentDevelopmentalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStudentDevelopmentalLabel">Record Student Developmental Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/DevelopmentalProfileController.php" method="post">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" id="edit_student_developmental_id">
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
                                <label for="edit_domain" class="form-label">Domain</label>
                                <select class="form-select" id="edit_domain" name="domain" required>
                                    <option value="">Select domain</option>
                                    <option value="Cognitive">Cognitive</option>
                                    <option value="Social">Social</option>
                                    <option value="Emotional">Emotional</option>
                                    <option value="Physical">Physical</option>
                                    <option value="Language">Language</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_observation" class="form-label">Observation</label>
                                <textarea class="form-control" id="edit_observation" name="observation" rows="3" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_recommendation" class="form-label">Recommendation</label>
                                <textarea class="form-control" id="edit_recommendation" name="recommendation" rows="3"></textarea>
                            </div>
                        </div>
                        <!-- hide the value of recorded_by to prevent changing the value -->
                        <input type="hidden" name="recorded_by" id="edit_recorded_by">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button
                            type="submit"
                            class="btn btn-primary" name="update_developmental_profile"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">Manage Student Developmental</h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>School Year</th>
                        <th>Domain</th>
                        <th>Recommendation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $developmentalRows   = $developmentals['data']         ?? [];
                        $developmentalPage   = $developmentals['current_page'] ?? 1;
                        $developmentalPages  = $developmentals['total_pages']  ?? 1;
                        $developmentalPer    = $developmentals['per_page']     ?? 10;
                        $developmentalOffset = ($developmentalPage - 1) * $developmentalPer;
                    ?>
                    <?php if(!empty($developmentalRows)): ?>
                        <?php foreach($developmentalRows as $index => $developmental): ?>
                            <tr>
                                <td><?php echo $developmentalOffset + $index + 1; ?></td>
                                <td><?= htmlspecialchars($developmental['student_first_name'] . ' ' . $developmental['student_last_name']); ?></td>
                                <td><?= htmlspecialchars($developmental['school_year']); ?></td>
                                <td><?= htmlspecialchars($developmental['domain']); ?></td>
                                <td><?= htmlspecialchars($developmental['recommendation']); ?></td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-warning edit-developmental-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#updateStudentDevelopmentalModal"
                                        data-id="<?= htmlspecialchars($developmental['id']); ?>"
                                        data-student-id="<?= htmlspecialchars($developmental['student_id']); ?>"
                                        data-school-year-id="<?= htmlspecialchars($developmental['school_year_id']); ?>"
                                        data-domain="<?= htmlspecialchars($developmental['domain']); ?>"
                                        data-observation="<?= htmlspecialchars($developmental['observation']); ?>"
                                        data-recommendation="<?= htmlspecialchars($developmental['recommendation'] ?? ''); ?>"
                                        data-recorded-by="<?= htmlspecialchars($developmental['recorded_by']); ?>"
                                    >
                                        Edit
                                    </button>

                                    <form action="../../../app/controllers/teacher/DevelopmentalProfileController.php" method="post" class="d-inline">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($developmental['id']); ?>">
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-danger"
                                            name="delete_developmental"
                                            onclick="return confirm('Are you sure you want to delete this record?this action cannot be undone.')"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No Developmental Profiles Found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php $developmentalQuery = $filter_student_id !== null ? 'student_id=' . $filter_student_id . '&' : ''; ?>
        <?php if ($developmentalPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $developmentalPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $developmentalQuery; ?>page=<?php echo $developmentalPage - 1; ?>">&laquo;</a>
              </li>
              <?php for ($p = 1; $p <= $developmentalPages; $p++): ?>
                <li class="page-item <?php echo $p === $developmentalPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?<?php echo $developmentalQuery; ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $developmentalPage >= $developmentalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $developmentalQuery; ?>page=<?php echo $developmentalPage + 1; ?>">&raquo;</a>
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
  <script src="../../../public/js/teacher/student-developmental.js"></script>
</body>
</html>