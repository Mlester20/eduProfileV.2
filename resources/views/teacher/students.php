<?php
require_once __DIR__ . '/../../../app/controllers/teacher/StudentsController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/StudentsAge.php';
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Students</title>
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
      <button 
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#createStudentModal"
      >
        Add Student
      </button>
    </div>

    <div class="modal fade" id="createStudentModal" tabindex="-1" aria-labelledby="createStudentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="createStudentLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/StudentsController.php" method="post">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="lrn" class="form-label">LRN</label>
                                <input class="form-control" type="text" name="lrn" id="lrn" placeholder="e.g., 123456789012" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input class="form-control" type="date" name="birth_date" id="birth_date" required>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input class="form-control" type="text" name="first_name" id="first_name" placeholder="e.g., Juan" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input class="form-control" type="text" name="middle_name" id="middle_name" placeholder="e.g., Liwaliw">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input class="form-control" type="text" name="last_name" id="last_name" placeholder="e.g., Dela Cruz" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="suffix" class="form-label">Suffix</label>
                                <input class="form-control" type="text" name="suffix" id="suffix" placeholder="e.g., Jr., Sr.">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input class="form-control" type="text" name="address" id="address" placeholder="e.g., 123 Main St, Barangay Example">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="school_year_id" class="form-label">School Year</label>
                                <select class="form-select" name="school_year_id" id="school_year_id">
                                    <option value="" selected disabled>-- Choose School Year --</option>
                                    <?php foreach (($school_years ?? []) as $school_year): ?>
                                        <option value="<?php echo htmlspecialchars($school_year['id']); ?>">
                                            <?php echo htmlspecialchars($school_year['school_year']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grade Level & Section</label>
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
                                    <!-- Multiple advisory sections: still restricted to this teacher's own sections -->
                                    <select class="form-select" name="section_id" id="section_id">
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
                                    <input type="hidden" name="grade_level_id" id="grade_level_id">
                                <?php else: ?>
                                    <input class="form-control" type="text" value="No section assigned to you yet" disabled>
                                    <div class="form-text text-danger">Contact an admin to get assigned as a section adviser before adding students.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <input type="hidden" name="recorded_by" value="<?php echo isset($_SESSION['id']) ? (int) $_SESSION['id'] : ''; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="create_student">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="editStudentLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/StudentsController.php" method="post">
                  <input type="hidden" name="id" id="edit_student_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="lrn" class="form-label">LRN</label>
                                <input class="form-control" type="text" name="lrn" id="edit_lrn" placeholder="Enter LRN">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input class="form-control" type="date" name="birth_date" id="edit_birth_date">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input class="form-control" type="text" name="first_name" id="edit_first_name" placeholder="First name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input class="form-control" type="text" name="middle_name" id="edit_middle_name" placeholder="Middle name">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input class="form-control" type="text" name="last_name" id="edit_last_name" placeholder="Last name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="suffix" class="form-label">Suffix</label>
                                <input class="form-control" type="text" name="suffix" id="edit_suffix" placeholder="Jr., Sr.">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" name="gender" id="edit_gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input class="form-control" type="text" name="address" id="edit_address" placeholder="House No., Street, Barangay">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="school_year_id" class="form-label">School Year</label>
                                <select class="form-select" name="school_year_id" id="edit_school_year_id">
                                    <option value="" selected disabled>-- Choose School Year --</option>
                                    <?php foreach (($school_years ?? []) as $school_year): ?>
                                        <option value="<?php echo htmlspecialchars($school_year['id']); ?>">
                                            <?php echo htmlspecialchars($school_year['school_year']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grade Level & Section</label>
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
                                    <!-- Multiple advisory sections: still restricted to this teacher's own sections -->
                                    <select class="form-select" name="section_id" id="edit_section_id">
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
                                    <input type="hidden" name="grade_level_id" id="edit_grade_level_id">
                                <?php else: ?>
                                    <input class="form-control" type="text" value="No section assigned to you yet" disabled>
                                    <div class="form-text text-danger">Contact an admin to get assigned as a section adviser before adding students.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <input type="hidden" name="recorded_by" value="<?php echo isset($_SESSION['id']) ? (int) $_SESSION['id'] : ''; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_student">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- view modal -->
    <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentLabel">Student Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#view_tab_profile" type="button">Profile</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#view_tab_behavior" type="button">Behavior Records</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#view_tab_developmental" type="button">Developmental Records</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="view_tab_profile">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">LRN</label>
                                    <p class="mb-0" id="view_student_lrn"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Student Name</label>
                                    <p class="mb-0" id="view_student_full_name"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Grade Level & Section</label>
                                    <p class="mb-0" id="view_student_section"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">School Year</label>
                                    <p class="mb-0" id="view_student_school_year"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Age</label>
                                    <p class="mb-0" id="view_student_age"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Gender</label>
                                    <p class="mb-0" id="view_student_gender"></p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Address</label>
                                    <p class="mb-0" id="view_student_address"></p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="view_tab_behavior">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Observation</th>
                                            <th>Intervention</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_behavior_records"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="view_tab_developmental">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>School Year</th>
                                            <th>Domain</th>
                                            <th>Observation</th>
                                            <th>Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_developmental_records"></tbody>
                                </table>
                            </div>
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
      <h5 class="card-header">Manage Students</h5>
        <div class="table-responsive nowrap">
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>LRN</th>
                <th>Student Name</th>
                <th>Grade Level & Section</th>
                <th>Age</th>
                <th>Actions</th>
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
                  <tr
                    role="button"
                    style="cursor: pointer;"
                    data-bs-toggle="modal"
                    data-bs-target="#viewStudentModal"
                    onclick="viewStudent(
                        '<?php echo $student['id']; ?>',
                        '<?php echo $student['lrn']; ?>',
                        '<?php echo htmlspecialchars($student['full_name']); ?>',
                        '<?php echo htmlspecialchars($student['grade_name'] . ' - ' . $student['section_name']); ?>',
                        '<?php echo htmlspecialchars($student['school_year']); ?>',
                        '<?php echo StudentsAge::calculateAge($student['birth_date']); ?>',
                        '<?php echo $student['gender']; ?>',
                        '<?php echo htmlspecialchars($student['address']); ?>'
                    )"
                  >
                    <td><?php echo $offset + $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($student['lrn']); ?></td>
                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['grade_name'] . ' - ' . $student['section_name']); ?></td>
                    <!-- dynamic age based on the birth date -->
                    <td><?php echo StudentsAge::calculateAge($student['birth_date']); ?></td>
                    <td>
                      <!-- update button -->
                      <button
                        class="btn btn-sm btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#editStudentModal"
                        onclick="event.stopPropagation(); editStudent(
                          '<?php echo $student['id']; ?>',
                          '<?php echo $student['lrn']; ?>',
                          '<?php echo $student['first_name']; ?>',
                          '<?php echo $student['middle_name']; ?>',
                          '<?php echo $student['last_name']; ?>',
                          '<?php echo $student['suffix']; ?>',
                          '<?php echo $student['birth_date']; ?>',
                          '<?php echo $student['gender']; ?>',
                          '<?php echo $student['address']; ?>',
                          '<?php echo $student['school_year_id']; ?>',
                          '<?php echo $student['grade_level_id']; ?>',
                          '<?php echo $student['section_id']; ?>',
                          '<?php echo $student['recorded_by']; ?>'
                        )"
                      >
                        Edit
                      </button>

                      <!-- delete method -->
                      <form action="../../../app/controllers/teacher/StudentsController.php" method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
                        <button
                          type="submit"
                          class="btn btn-sm btn-danger"
                          name="delete_student"
                          onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this student? this action cannot be undone.');"
                        >
                          Delete
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">No students found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">&laquo;</a>
              </li>
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">&raquo;</a>
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
    <script>
        const studentBehaviorRecords = <?php echo json_encode($behavior_by_student ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const studentDevelopmentalRecords = <?php echo json_encode($developmental_by_student ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    </script>
<script src="../../../public/js/teacher/home.js"></script>
<script src="../../../public/js/teacher/students.js"></script>
</body>
</html>