<?php
require_once __DIR__ . '/../../../app/controllers/teacher/AcademicProfileController.php';
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

    <div class="text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAcademicProfileModal">
            <i class="bx bx-plus"></i> Add Academic Profile
        </button>
    </div>

    <!-- add modal -->
    <div class="modal fade" id="addAcademicProfileModal" tabindex="-1" aria-labelledby="addAcademicProfileLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAcademicProfileLabel">Add Academic Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/AcademicProfileController.php" method="post">
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
                                    <option value="" selected disabled>-- Choose Student --</option>
                                    <?php foreach(($students ?? []) as $student): ?>
                                        <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'] . ' ' . ($student['suffix'] ?? '')); ?>
                                        <option value="<?php echo htmlspecialchars($student['id']); ?>"><?php echo htmlspecialchars($studentName); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_year_id" class="form-label">School Year</label>
                                <select class="form-select" id="school_year_id" name="school_year_id" required>
                                    <option value="" selected disabled>-- Choose School Year --</option>
                                    <?php foreach(($school_years ?? []) as $school_year): ?>
                                        <option value="<?php echo htmlspecialchars($school_year['id']); ?>"><?php echo htmlspecialchars($school_year['school_year']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="subject_name" class="form-label">Subject Name</label>
                                <input class="form-control" type="text" name="subject_name" id="subject_name" placeholder="Enter subject name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="grading_period" class="form-label">Grading Period</label>
                                <select class="form-select" name="grading_period" id="grading_period" required>
                                    <option value="" selected disabled>-- Choose Grading Period --</option>
                                    <option value="1st Quarter">1st Quarter</option>
                                    <option value="2nd Quarter">2nd Quarter</option>
                                    <option value="3rd Quarter">3rd Quarter</option>
                                    <option value="4th Quarter">4th Quarter</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="grade" class="form-label">Grade</label>
                                <input class="form-control" type="number" step="0.01" min="0" max="100" name="grade" id="grade" placeholder="Enter grade" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="2" placeholder="Enter remarks"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_academic_profile">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- edit modal -->
    <div class="modal fade" id="editAcademicProfileModal" tabindex="-1" aria-labelledby="editAcademicProfileLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAcademicProfileLabel">Edit Academic Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/AcademicProfileController.php" method="post">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" id="edit_academic_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_student_id_search" class="form-label">Student</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="edit_student_id_search" placeholder="Search student..." autocomplete="off">
                                    <div id="edit_student_id_suggestions" class="list-group shadow-sm position-absolute w-100" style="top: 100%; left: 0; z-index: 5; max-height: 200px; overflow-y: auto; display: none;"></div>
                                </div>
                                <select class="form-select" name="student_id" id="edit_student_id" required>
                                    <option value="" selected disabled>-- Choose Student --</option>
                                    <?php foreach(($students ?? []) as $student): ?>
                                        <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'] . ' ' . ($student['suffix'] ?? '')); ?>
                                        <option value="<?php echo htmlspecialchars($student['id']); ?>"><?php echo htmlspecialchars($studentName); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_school_year_id" class="form-label">School Year</label>
                                <select class="form-select" name="school_year_id" id="edit_school_year_id" required>
                                    <option value="" selected disabled>-- Choose School Year --</option>
                                    <?php foreach(($school_years ?? []) as $school_year): ?>
                                        <option value="<?php echo htmlspecialchars($school_year['id']); ?>"><?php echo htmlspecialchars($school_year['school_year']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_subject_name" class="form-label">Subject Name</label>
                                <input class="form-control" type="text" name="subject_name" id="edit_subject_name" placeholder="Enter subject name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_grading_period" class="form-label">Grading Period</label>
                                <select class="form-select" name="grading_period" id="edit_grading_period" required>
                                    <option value="" selected disabled>-- Choose Grading Period --</option>
                                    <option value="1st Quarter">1st Quarter</option>
                                    <option value="2nd Quarter">2nd Quarter</option>
                                    <option value="3rd Quarter">3rd Quarter</option>
                                    <option value="4th Quarter">4th Quarter</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_grade" class="form-label">Grade</label>
                                <input class="form-control" type="number" step="0.01" min="0" max="100" name="grade" id="edit_grade" placeholder="Enter grade" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" id="edit_remarks" rows="2" placeholder="Enter remarks"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_academic_profile">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">Academic Profiles</h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Subject Name</th>
                        <th>Grading Period</th>
                        <th>Grade</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $academicRows   = $academicProfiles['data']         ?? [];
                        $academicPage   = $academicProfiles['current_page'] ?? 1;
                        $academicPages  = $academicProfiles['total_pages']  ?? 1;
                        $academicPer    = $academicProfiles['per_page']     ?? 10;
                        $academicOffset = ($academicPage - 1) * $academicPer;
                    ?>
                    <?php foreach($academicRows as $index => $profile): ?>
                        <tr>
                            <td><?php echo $academicOffset + $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($profile['student_first_name'] . ' ' . $profile['student_middle_name'] . ' ' . $profile['student_last_name'] . ' ' . $profile['student_suffix']); ?></td>
                            <td><?php echo htmlspecialchars($profile['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($profile['grading_period']); ?></td>
                            <td><?php echo htmlspecialchars($profile['grade']); ?></td>
                            <td><?php echo htmlspecialchars($profile['remarks']); ?></td>
                            <td>
                                <button
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editAcademicProfileModal"
                                    onclick="editAcademicProfile(
                                        '<?php echo htmlspecialchars($profile['id']); ?>',
                                        '<?php echo htmlspecialchars($profile['student_id']); ?>',
                                        '<?php echo htmlspecialchars($profile['school_year_id']); ?>',
                                        '<?php echo htmlspecialchars($profile['subject_name']); ?>',
                                        '<?php echo htmlspecialchars($profile['grading_period']); ?>',
                                        '<?php echo htmlspecialchars($profile['grade']); ?>',
                                        '<?php echo htmlspecialchars($profile['remarks']); ?>'
                                    )"
                                >
                                    <i class="bx bx-edit"></i>
                                </button>
                                
                                <form action="../../../app/controllers/teacher/AcademicProfileController.php" method="post" class="d-inline">
                                    <?= Csrf::field() ?>
                                
                                    <input type="hidden" name="id" value="<?php echo $profile['id']; ?>">
                                    <button 
                                        type="submit" 
                                        class="btn btn-sm btn-danger" 
                                        name="delete_academic_profile"
                                        onclick="return confirm('Are you sure you want to delete this academic profile?');">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($academicPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $academicPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $academicPage - 1; ?>">&laquo;</a>
              </li>
              <?php for ($p = 1; $p <= $academicPages; $p++): ?>
                <li class="page-item <?php echo $p === $academicPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $academicPage >= $academicPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $academicPage + 1; ?>">&raquo;</a>
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
  <script src="../../../public/js/teacher/home.js"></script>
  <script src="../../../public/js/teacher/academic.js"></script>
</body>
</html>