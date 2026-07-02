<?php
require_once __DIR__ . '/../../../app/controllers/teacher/ParentGuardianController.php';
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Parent & Guardian</title>
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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addParentModal">
            Add Parent/Guardian
        </button>
    </div>

    <!-- Add Parent/Guardian Modal -->
    <div class="modal fade" id="addParentModal" tabindex="-1" aria-labelledby="addParentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addParentLabel">Add Parent/Guardian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/ParentGuardianController.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="recorded_by" value="">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select" name="student_id" id="student_id" required>
                                <option value="" selected disabled>-- Select Student --</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= htmlspecialchars($student['id']) ?>">
                                        <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?>
                                        <?php if (!empty($student['grade_name'])): ?>
                                            (<?= htmlspecialchars($student['grade_name']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr>

                        <h6 class="mb-3">Father's Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="father_name" class="form-label">Father's Name</label>
                                <input class="form-control" type="text" name="father_name" id="father_name" placeholder="Full name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="father_occupation" class="form-label">Occupation</label>
                                <input class="form-control" type="text" name="father_occupation" id="father_occupation" placeholder="Occupation">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="father_contact" class="form-label">Contact Number</label>
                                <input class="form-control" type="text" name="father_contact" id="father_contact" placeholder="e.g., 09XXXXXXXXX">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Mother's Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="mother_name" class="form-label">Mother's Name</label>
                                <input class="form-control" type="text" name="mother_name" id="mother_name" placeholder="Full name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="mother_occupation" class="form-label">Occupation</label>
                                <input class="form-control" type="text" name="mother_occupation" id="mother_occupation" placeholder="Occupation">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="mother_contact" class="form-label">Contact Number</label>
                                <input class="form-control" type="text" name="mother_contact" id="mother_contact" placeholder="e.g., 09XXXXXXXXX">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Guardian's Information <small class="text-muted">(if applicable)</small></h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="guardian_name" class="form-label">Guardian's Name</label>
                                <input class="form-control" type="text" name="guardian_name" id="guardian_name" placeholder="Full name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="guardian_relationship" class="form-label">Relationship to Student</label>
                                <input class="form-control" type="text" name="guardian_relationship" id="guardian_relationship" placeholder="e.g., Aunt, Grandparent">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="guardian_contact" class="form-label">Contact Number</label>
                                <input class="form-control" type="text" name="guardian_contact" id="guardian_contact" placeholder="e.g., 09XXXXXXXXX">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button 
                            type="submit" 
                            class="btn btn-primary" 
                            name="create_parent_guardian"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Parent/Guardian Modal -->
    <div class="modal fade" id="editParentModal" tabindex="-1" aria-labelledby="editParentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editParentLabel">Edit Parent/Guardian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/ParentGuardianController.php" method="post">
                    <input type="hidden" name="id" id="edit_parent_id" value="<?= htmlspecialchars($parentGuardian['id'] ?? '') ?>">
                    <div class="modal-body">
                        <input type="hidden" name="recorded_by" value="">

                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select" name="student_id" id="edit_student_id" required>
                                <option value="" selected disabled>-- Select Student --</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= htmlspecialchars($student['id']) ?>">
                                        <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?>
                                        <?php if (!empty($student['grade_name'])): ?>
                                            (<?= htmlspecialchars($student['grade_name']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr>

                        <h6 class="mb-3">Father's Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="father_name" class="form-label">Father's Name</label>
                                <input class="form-control" type="text" name="father_name" id="edit_father_name" placeholder="Full name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="father_occupation" class="form-label">Occupation</label>
                                <input class="form-control" type="text" name="father_occupation" id="edit_father_occupation" placeholder="Occupation">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="father_contact" class="form-label">Contact Number</label>
                                <input class="form-control" type="text" name="father_contact" id="edit_father_contact" placeholder="e.g., 09XXXXXXXXX">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Mother's Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="mother_name" class="form-label">Mother's Name</label>
                                <input class="form-control" type="text" name="mother_name" id="edit_mother_name" placeholder="Full name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="mother_occupation" class="form-label">Occupation</label>
                                <input class="form-control" type="text" name="mother_occupation" id="edit_mother_occupation" placeholder="Occupation">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="mother_contact" class="form-label">Contact Number</label>
                                <input class="form-control" type="text" name="mother_contact" id="edit_mother_contact" placeholder="e.g., 09XXXXXXXXX">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Guardian's Information <small class="text-muted">(if applicable)</small></h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="guardian_name" class="form-label">Guardian's Name</label>
                                <input class="form-control" type="text" name="guardian_name" id="edit_guardian_name" placeholder="Full name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="guardian_relationship" class="form-label">Relationship to Student</label>
                                <input class="form-control" type="text" name="guardian_relationship" id="edit_guardian_relationship" placeholder="e.g., Aunt, Grandparent">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="guardian_contact" class="form-label">Contact Number</label>
                                <input class="form-control" type="text" name="guardian_contact" id="edit_guardian_contact" placeholder="e.g., 09XXXXXXXXX">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            name="update_parent_guardian"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">Parent & Guardian Information</h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Guardian Name</th>
                        <th>Contact Number</th>
                        <th>Relationship</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($parentGuardians)) : ?>
                        <?php foreach ($parentGuardians as $index => $parentGuardian) : ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($parentGuardian['student_first_name'] . ' ' . $parentGuardian['student_middle_name'] . ' ' . $parentGuardian['student_last_name'] . ' ' . $parentGuardian['student_suffix']) ?></td>
                                <td><?= htmlspecialchars($parentGuardian['guardian_name']) ?></td>
                                <td><?= htmlspecialchars($parentGuardian['guardian_contact']) ?></td>
                                <td><?= htmlspecialchars($parentGuardian['guardian_relationship']) ?></td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editParentModal"
                                        data-id="<?= htmlspecialchars($parentGuardian['id']) ?>"
                                        data-student-id="<?= htmlspecialchars($parentGuardian['student_id']) ?>"
                                        data-father-name="<?= htmlspecialchars($parentGuardian['father_name']) ?>"
                                        data-father-occupation="<?= htmlspecialchars($parentGuardian['father_occupation']) ?>"
                                        data-father-contact="<?= htmlspecialchars($parentGuardian['father_contact']) ?>"
                                        data-mother-name="<?= htmlspecialchars($parentGuardian['mother_name']) ?>"
                                        data-mother-occupation="<?= htmlspecialchars($parentGuardian['mother_occupation']) ?>"
                                        data-mother-contact="<?= htmlspecialchars($parentGuardian['mother_contact']) ?>"
                                        data-guardian-name="<?= htmlspecialchars($parentGuardian['guardian_name']) ?>"
                                        data-guardian-relationship="<?= htmlspecialchars($parentGuardian['guardian_relationship']) ?>"
                                        data-guardian-contact="<?= htmlspecialchars($parentGuardian['guardian_contact']) ?>"
                                        onclick="editParentGuardian(this)"
                                    >Edit</button>
                                    
                                    <form action="../../../app/controllers/teacher/ParentGuardianController.php" method="post" class="d-inline">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($parentGuardian['id']); ?>">
                                        <button 
                                            type="submit" 
                                            name="delete_parent_guardian"
                                            class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this record?');">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">No parent/guardian records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
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
    <script src="../../../public/js/teacher/parent-guardian.js"></script>
</body>
</html>