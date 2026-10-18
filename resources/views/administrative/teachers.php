<?php
require_once __DIR__ . '/../../../app/controllers/administrative/teacherscontroller.php';
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
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Manage Teachers</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../../../public/assets/img/favicon/logo.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />
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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
            Add Teacher
        </button>
    </div>

    <!-- Add Teacher -->
    <div class="modal fade" id="createTeacherModal" tabindex="-1" aria-labelledby="createTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTeacherModalLabel">Add Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="teacherForm" method="POST" action="../../../app/controllers/administrative/teacherscontroller.php">
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="full_name" placeholder="e.g., Juan Dela Cruz" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="e.g., juan.delacruz@school.edu.ph" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="e.g., 09171234567">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="sex" class="form-label">Sex</label>
                            <select class="form-select" id="sex" name="sex">
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="e.g., San Jose Sur, Mallig, Isabela">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter a strong password" minlength="8" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="createUser">Save Teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Teacher -->
    <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTeacherModalLabel">Edit Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTeacherForm" method="POST" action="../../../app/controllers/administrative/teacherscontroller.php">
                        <?= Csrf::field() ?>
                        <input type="hidden" id="editTeacherId" name="id">
                        <div class="mb-3">
                            <label for="editFullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editFullName" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="editContactNumber" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="editContactNumber" name="contact_number" placeholder="e.g., 09171234567">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editBirthDate" class="form-label">Birth Date</label>
                                <input type="date" class="form-control" id="editBirthDate" name="birth_date">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editSex" class="form-label">Sex</label>
                            <select class="form-select" id="editSex" name="sex">
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="editAddress" name="address" placeholder="e.g., San Jose Sur, Mallig, Isabela">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="updateUser">Update Teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password -->
    <div class="modal fade" id="resetTeacherPasswordModal" tabindex="-1" aria-labelledby="resetTeacherPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetTeacherPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="resetTeacherPasswordForm" method="POST" action="../../../app/controllers/administrative/teacherscontroller.php">
                        <?= Csrf::field() ?>
                        <input type="hidden" id="resetTeacherPasswordId" name="id">
                        <p class="mb-3">Setting a new password for <strong id="resetTeacherPasswordName"></strong>.</p>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="resetUserPassword">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">Manage Teachers</h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($teachers)): ?>
                        <?php foreach($teachers as $index => $teacher): ?>
                            <?php $isActive = ($teacher['status'] ?? 'active') === 'active'; ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($teacher['full_name']) ?></td>
                                <td><?= htmlspecialchars($teacher['email']) ?></td>
                                <td>
                                    <span class="badge bg-label-<?= $isActive ? 'success' : 'secondary' ?>">
                                        <?= $isActive ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTeacherModal"
                                        data-id="<?= htmlspecialchars($teacher['id']) ?>"
                                        data-full-name="<?= htmlspecialchars($teacher['full_name']) ?>"
                                        data-email="<?= htmlspecialchars($teacher['email']) ?>"
                                        data-contact-number="<?= htmlspecialchars($teacher['contact_number'] ?? '') ?>"
                                        data-address="<?= htmlspecialchars($teacher['address'] ?? '') ?>"
                                        data-birth-date="<?= htmlspecialchars($teacher['birth_date'] ?? '') ?>"
                                        data-sex="<?= htmlspecialchars($teacher['sex'] ?? '') ?>"
                                        onclick="editTeacher(this.dataset)"
                                        title="Edit"
                                        >
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#resetTeacherPasswordModal"
                                        onclick="resetTeacherPassword(
                                            '<?= htmlspecialchars($teacher['id']) ?>',
                                            '<?= htmlspecialchars($teacher['full_name']) ?>'
                                        )"
                                        title="Reset Password"
                                        >
                                        <i class="bx bx-key"></i>
                                    </button>

                                    <form action="../../../app/controllers/administrative/teacherscontroller.php" method="POST" style="display: inline;">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id']) ?>">
                                        <input type="hidden" name="status" value="<?= $isActive ? 'inactive' : 'active' ?>">
                                        <button
                                            type="submit"
                                            class="btn btn-sm <?= $isActive ? 'btn-danger' : 'btn-success' ?>"
                                            name="toggleUserStatus"
                                            title="<?= $isActive ? 'Deactivate' : 'Activate' ?>"
                                            onclick="return confirm('Are you sure you want to <?= $isActive ? 'deactivate' : 'activate' ?> this teacher?')"
                                            >
                                            <i class="bx <?= $isActive ? 'bx-block' : 'bx-check-circle' ?>"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No teacher accounts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

    <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
    <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
    <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../../public/assets/vendor/js/menu.js"></script>
    <script src="../../../public/assets/js/main.js"></script>
    <script src="../../../public/js/administrative/teachers.js"></script>
</body>
</html>
