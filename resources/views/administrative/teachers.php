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
    <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>" />
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTeacherModalLabel">Add Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="teacherForm" method="POST" action="../../../app/controllers/administrative/teacherscontroller.php">
                        <?= Csrf::field() ?>
                        <div class="row g-3">
                            <div class="col-6 col-md-6 mb-3">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullName" name="full_name" placeholder="e.g., Juan Dela Cruz" title="Letters, spaces, and hyphens only." pattern="[A-Za-z]+([ -][A-Za-z]+)*" required>
                            </div>
                            <div class="col-6 col-md-6 mb-3">
                                <label for="position" class="form-label">Position</label>
                                <select class="form-select" id="position">
                                    <option value="">Select Position</option>
                                    <option value="Teacher I">Teacher I</option>
                                    <option value="Teacher II">Teacher II</option>
                                    <option value="Teacher III">Teacher III</option>
                                    <option value="Master Teacher I">Master Teacher I</option>
                                    <option value="Master Teacher II">Master Teacher II</option>
                                    <option value="Master Teacher III">Master Teacher III</option>
                                    <option value="Master Teacher IV">Master Teacher IV</option>
                                    <option value="Head Teacher I">Head Teacher I</option>
                                    <option value="Head Teacher II">Head Teacher II</option>
                                    <option value="Head Teacher III">Head Teacher III</option>
                                    <option value="Head Teacher IV">Head Teacher IV</option>
                                    <option value="Head Teacher V">Head Teacher V</option>
                                    <option value="__other__">Other</option>
                                </select>
                                <input class="form-control mt-1 d-none" type="text" id="position_other" placeholder="Enter position">
                                <input type="hidden" id="position_value" name="position">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6 col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="e.g., juan.delacruz@school.edu.ph" required>
                            </div>
                            <div class="col-6 col-md-6 mb-3">
                                <label for="sex" class="form-label">Sex</label>
                                <select class="form-select" id="sex" name="sex">
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6 col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="number" class="form-control" id="contact_number" name="contact_number" placeholder="e.g., 09171234567" inputmode="numeric" pattern="\d{11}" maxlength="11" title="Contact number must be exactly 11 digits, numbers only">
                            </div>
                            <div class="col-6 col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6 col-md-4 mb-3">
                                <label for="province" class="form-label">Province</label>
                                <select class="form-select" id="province">
                                    <option value="">Loading provinces...</option>
                                </select>
                                <input class="form-control mt-1 d-none" type="text" id="province_other" placeholder="Enter province">
                            </div>
                            <div class="col-6 col-md-4 mb-3">
                                <label for="city_municipality" class="form-label">City / Municipality</label>
                                <select class="form-select" id="city_municipality" disabled>
                                    <option value="">Select province first</option>
                                </select>
                                <input class="form-control mt-1 d-none" type="text" id="city_municipality_other" placeholder="Enter city/municipality">
                            </div>
                            <div class="col-6 col-md-4 mb-3">
                                <label for="barangay" class="form-label">Barangay</label>
                                <select class="form-select" id="barangay" disabled>
                                    <option value="">Select city/municipality first</option>
                                </select>
                                <input class="form-control mt-1 d-none" type="text" id="barangay_other" placeholder="Enter barangay">
                            </div>
                        </div>
                        <input type="hidden" id="province_value" name="province">
                        <input type="hidden" id="city_municipality_value" name="city_municipality">
                        <input type="hidden" id="barangay_value" name="barangay">
                        <div class="row g-3">
                            <div class="col-6 col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter a strong password" minlength="8" required>
                            </div>
                            <div class="col-6 col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter the password" minlength="8" required>
                            </div>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTeacherModalLabel">Edit Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTeacherForm" method="POST" action="../../../app/controllers/administrative/teacherscontroller.php">
                        <?= Csrf::field() ?>
                        <input type="hidden" id="editTeacherId" name="id">
                        <div class="row g-3">
                            <div class="col-6 col-md-6 mb-3">
                                <label for="editFullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="editFullName" name="full_name" title="Letters, spaces, and hyphens only." pattern="[A-Za-z]+([ -][A-Za-z]+)*" required>
                            </div>
                            <div class="col-6 col-md-6 mb-3">
                                <label for="editPosition" class="form-label">Position</label>
                                <select class="form-select" id="editPosition">
                                    <option value="">Select Position</option>
                                    <option value="Teacher I">Teacher I</option>
                                    <option value="Teacher II">Teacher II</option>
                                    <option value="Teacher III">Teacher III</option>
                                    <option value="Master Teacher I">Master Teacher I</option>
                                    <option value="Master Teacher II">Master Teacher II</option>
                                    <option value="Master Teacher III">Master Teacher III</option>
                                    <option value="Master Teacher IV">Master Teacher IV</option>
                                    <option value="Head Teacher I">Head Teacher I</option>
                                    <option value="Head Teacher II">Head Teacher II</option>
                                    <option value="Head Teacher III">Head Teacher III</option>
                                    <option value="Head Teacher IV">Head Teacher IV</option>
                                    <option value="Head Teacher V">Head Teacher V</option>
                                    <option value="__other__">Other</option>
                                </select>
                                <input class="form-control mt-1 d-none" type="text" id="editPosition_other" placeholder="Enter position">
                                <input type="hidden" id="editPosition_value" name="position">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6 col-md-6 mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email" required>
                            </div>
                            <div class="col-6 col-md-6 mb-3">
                                <label for="editSex" class="form-label">Sex</label>
                                <select class="form-select" id="editSex" name="sex">
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6 col-md-6 mb-3">
                                <label for="editContactNumber" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="editContactNumber" name="contact_number" placeholder="e.g., 09171234567" inputmode="numeric" pattern="\d{11}" maxlength="11" title="Contact number must be exactly 11 digits, numbers only">
                            </div>
                            <div class="col-6 col-md-6 mb-3">
                                <label for="editBirthDate" class="form-label">Birth Date</label>
                                <input type="date" class="form-control" id="editBirthDate" name="birth_date">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6 col-md-4 mb-3">
                                <label for="editProvince" class="form-label">Province</label>
                                <select class="form-select" id="editProvince">
                                    <option value="">Loading provinces...</option>
                                </select>
                                <input class="form-control mt-1 d-none" type="text" id="editProvince_other" placeholder="Enter province">
                            </div>
                            <div class="col-6 col-md-4 mb-3">
                                <label for="editCityMunicipality" class="form-label">City / Municipality</label>
                                <select class="form-select" id="editCityMunicipality" disabled>
                                    <option value="">Select province first</option>
                                </select>
                                <input class="form-control mt-1 d-none" type="text" id="editCityMunicipality_other" placeholder="Enter city/municipality">
                            </div>
                            <div class="col-6 col-md-4 mb-3">
                                <label for="editBarangay" class="form-label">Barangay</label>
                                <select class="form-select" id="editBarangay" disabled>
                                    <option value="">Select city/municipality first</option>
                                </select>
                                <input class="form-control mt-1 d-none" type="text" id="editBarangay_other" placeholder="Enter barangay">
                            </div>
                        </div>
                        <input type="hidden" id="editProvince_value" name="province">
                        <input type="hidden" id="editCityMunicipality_value" name="city_municipality">
                        <input type="hidden" id="editBarangay_value" name="barangay">
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

    <div class="modal fade" id="assignSubjectsModal" tabindex="-1" aria-labelledby="assignSubjectsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignSubjectsModalLabel">Assign Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="assignSubjectsTeacherId">
                    <p class="mb-3">Subjects taught by <strong id="assignSubjectsTeacherName"></strong>. Only these will appear in their Academic Records dropdown.</p>
                    <div id="assignSubjectsList">
                        <p class="text-muted small mb-0">Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="assignSubjectsSaveBtn" onclick="saveAssignSubjects()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>Manage Teachers</span>
            <div class="btn-group" role="group">
                <a href="teachers.php?status=active" class="btn btn-sm <?= $status_filter === 'active' ? 'btn-primary' : 'btn-outline-primary' ?>">Active</a>
                <a href="teachers.php?status=archived" class="btn btn-sm <?= $status_filter === 'archived' ? 'btn-primary' : 'btn-outline-primary' ?>">Archived</a>
            </div>
        </h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Position</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($teachers)): ?>
                        <?php foreach($teachers as $index => $teacher): ?>
                            <?php
                                $teacherStatus = $teacher['status'] ?? 'active';
                                $isActive = $teacherStatus === 'active';
                                $isArchived = $teacherStatus === 'archived';
                                $statusBadge = $isArchived ? 'danger' : ($isActive ? 'success' : 'secondary');
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><a href="teacher-profile.php?id=<?= (int) $teacher['id'] ?>"><?= htmlspecialchars($teacher['full_name']) ?></a></td>
                                <td><?= htmlspecialchars($teacher['position'] ?? '') ?: '<span class="text-muted">—</span>' ?></td>
                                <td><?= htmlspecialchars($teacher['email']) ?></td>
                                <td>
                                    <span class="badge bg-label-<?= $statusBadge ?>">
                                        <?= htmlspecialchars(ucfirst($teacherStatus)) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($isArchived): ?>
                                        <form action="../../../app/controllers/administrative/teacherscontroller.php" method="POST" style="display: inline;">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id']) ?>">
                                            <input type="hidden" name="status" value="active">
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-success"
                                                name="toggleUserStatus"
                                                title="Unarchive"
                                                onclick="return confirm('Restore this teacher from the archive? They will be marked Active again.')"
                                                >
                                                <i class="bx bx-undo"></i> Unarchive
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:void(0);"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editTeacherModal"
                                                    data-id="<?= htmlspecialchars($teacher['id']) ?>"
                                                    data-full-name="<?= htmlspecialchars($teacher['full_name']) ?>"
                                                    data-position="<?= htmlspecialchars($teacher['position'] ?? '') ?>"
                                                    data-email="<?= htmlspecialchars($teacher['email']) ?>"
                                                    data-contact-number="<?= htmlspecialchars($teacher['contact_number'] ?? '') ?>"
                                                    data-province="<?= htmlspecialchars($teacher['province'] ?? '') ?>"
                                                    data-city-municipality="<?= htmlspecialchars($teacher['city_municipality'] ?? '') ?>"
                                                    data-barangay="<?= htmlspecialchars($teacher['barangay'] ?? '') ?>"
                                                    data-birth-date="<?= htmlspecialchars($teacher['birth_date'] ?? '') ?>"
                                                    data-sex="<?= htmlspecialchars($teacher['sex'] ?? '') ?>"
                                                    onclick="editTeacher(this.dataset)"
                                                >
                                                    <i class="bx bx-edit me-1"></i> Edit
                                                </a>
                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:void(0);"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#resetTeacherPasswordModal"
                                                    onclick="resetTeacherPassword(
                                                        '<?= htmlspecialchars($teacher['id']) ?>',
                                                        '<?= htmlspecialchars($teacher['full_name']) ?>'
                                                    )"
                                                >
                                                    <i class="bx bx-key me-1"></i> Reset Password
                                                </a>
                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:void(0);"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#assignSubjectsModal"
                                                    onclick="openAssignSubjects(
                                                        '<?= htmlspecialchars($teacher['id']) ?>',
                                                        '<?= htmlspecialchars(addslashes($teacher['full_name'])) ?>'
                                                    )"
                                                >
                                                    <i class="bx bx-book-content me-1"></i> Assign Subjects
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form action="../../../app/controllers/administrative/teacherscontroller.php" method="POST">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id']) ?>">
                                                    <input type="hidden" name="status" value="<?= $isActive ? 'inactive' : 'active' ?>">
                                                    <button
                                                        type="submit"
                                                        class="dropdown-item <?= $isActive ? 'text-danger' : 'text-success' ?>"
                                                        name="toggleUserStatus"
                                                        onclick="return confirm('Are you sure you want to <?= $isActive ? 'deactivate' : 'activate' ?> this teacher?')"
                                                    >
                                                        <i class="bx <?= $isActive ? 'bx-block' : 'bx-check-circle' ?> me-1"></i> <?= $isActive ? 'Deactivate' : 'Activate' ?>
                                                    </button>
                                                </form>
                                                <form action="../../../app/controllers/administrative/teacherscontroller.php" method="POST">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id']) ?>">
                                                    <input type="hidden" name="status" value="archived">
                                                    <button
                                                        type="submit"
                                                        class="dropdown-item"
                                                        name="toggleUserStatus"
                                                        onclick="return confirm('Archive this teacher? They will be hidden from the main Manage Teachers list until unarchived.')"
                                                    >
                                                        <i class="bx bx-archive-in me-1"></i> Archive
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No <?= $status_filter === 'archived' ? 'archived' : '' ?> teacher accounts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
    <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
    <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../../public/assets/vendor/js/menu.js"></script>
    <script src="../../../public/assets/js/main.js"></script>
    <script src="../../../public/js/administrative/teachers.js"></script>
</body>
</html>
