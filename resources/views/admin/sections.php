<?php
require_once __DIR__ . '/../../../app/controllers/admin/SectionsController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['admin']);
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Sections</title>
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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSectionsModal">Add Section</button>
    </div>

    <!-- modal -->
    <div class="modal fade" id="createSectionsModal" tabindex="-1" aria-labelledby="createSectionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="createSectionLabel">Add Grade Level</h5>
                    <button type="button" class="btn-close" data-bs-dismis="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/admin/SectionsController.php" method="post">
                    <?= Csrf::field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="grade_level_id" class="form-label">Select Grade Level</label>
                            <select name="grade_level_id" id="grade_level_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Section --</option>
                                <?php foreach ($grade_levels as $grade_level): ?>
                                    <option value="<?= htmlspecialchars($grade_level['id']); ?>">
                                        <?= htmlspecialchars($grade_level['grade_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="grade_level" class="form-label">Grade Level</label>
                            <input 
                                class="form-control"
                                type="text"
                                name="section_name"
                                placeholder="e.g., Mahogani"
                            >
                        </div>
                        <div class="mb-3">
                            <label for="adviser_id" class="form-label">Select Advisor / Teacher</label>
                            <select name="adviser_id" id="adviser_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Teacher --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= htmlspecialchars($teacher['id']); ?>">
                                        <?= htmlspecialchars($teacher['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary">Close</button>
                        <button 
                            type="submit" 
                            class="btn btn-primary" name="create_section"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- update modal -->
    <div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="editSectionLabel">Add Grade Level</h5>
                    <button type="button" class="btn-close" data-bs-dismis="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/admin/SectionsController.php" method="post">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" id="edit_section_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="grade_level_id" class="form-label">Select Grade Level</label>
                            <select name="grade_level_id" id="edit_grade_level_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Section --</option>
                                <?php foreach ($grade_levels as $grade_level): ?>
                                    <option value="<?= htmlspecialchars($grade_level['id']); ?>">
                                        <?= htmlspecialchars($grade_level['grade_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="grade_level" class="form-label">Grade Level</label>
                            <input 
                                class="form-control"
                                type="text"
                                name="section_name"
                                id="edit_section_name"
                            >
                        </div>
                        <div class="mb-3">
                            <label for="adviser_id" class="form-label">Select Advisor / Teacher</label>
                            <select name="adviser_id" id="edit_adviser_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Teacher --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= htmlspecialchars($teacher['id']); ?>">
                                        <?= htmlspecialchars($teacher['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary">Close</button>
                        <button 
                            type="submit" 
                            class="btn btn-primary" name="update_section"
                        >
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">Manage Sections</h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Grade & Section</th>
                        <th>Assigned Teacher</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($sections)): ?>
                        <?php foreach ($sections as $section): ?>
                            <tr>
                                <td><?= htmlspecialchars($section['id']); ?></td>
                                <td><?= htmlspecialchars($section['grade_level_name'] . ' - ' . $section['section_name'] ); ?></td>
                                <td><?= htmlspecialchars($section['assigned_teacher']); ?></td>
                                <td><?= (new DateTime($section['created_at']))->format('F j') ?></td>
                                <td>
                                    <button 
                                        class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editSectionModal"
                                        onclick="editSection(
                                            '<?= htmlspecialchars($section['id']); ?>',
                                            '<?= htmlspecialchars($section['grade_level_id']); ?>',
                                            '<?= htmlspecialchars($section['section_name']); ?>',
                                            '<?= htmlspecialchars($section['adviser_id']); ?>'
                                        )"
                                    >
                                        Edit
                                    </button>

                                    <form action="../../../app/controllers/admin/SectionsController.php" method="post" style="display: inline;">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($section['id']); ?>">
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-danger"
                                            name="delete_section"
                                            onclick="return confirm('Are you sure you want to delete this section? this action cannot be undone.')"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No Sections Found, Create One.</td>
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
    <script src="../../../public/js/admin/sections.js"></script>
</body>
</html>