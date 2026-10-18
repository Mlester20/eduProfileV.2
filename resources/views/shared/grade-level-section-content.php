<?php
require_once __DIR__ . "/../../../app/controllers/{$role}/GradeLevelsController.php";
require_once __DIR__ . "/../../../app/controllers/{$role}/SectionsController.php";
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly([$role]);
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Grade Levels & Section</title>
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
<body data-role="<?= htmlspecialchars($role) ?>">

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . "/../{$role}/partials/sidebar.php"; ?>
    <?php require_once __DIR__ . "/../{$role}/partials/topbar.php"; ?>

    <!--
        Grade Levels have no standalone table anymore — every row here is a
        Section, shown as "Grade - Section" together. A brand-new grade
        level (e.g. "Grade 7") is created inline from the Add/Edit Section
        form itself (the "+ Add New Grade Level" option below), and the
        small pencil/trash icons next to that dropdown reuse the original
        Grade Level edit/delete modal+endpoint for renaming or removing one
        that's no longer tied to the form you're filling out.
    -->

    <!-- kept for the rename shortcut next to the grade level dropdowns below -->
    <div class="modal fade" id="editGradeLevel" tabindex="-1" aria-labelledby="editGradeLevelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="editGradeLevelLabel">Rename Grade Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/<?= htmlspecialchars($role) ?>/GradeLevelsController.php" method="post">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" id="edit_grade_level_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_grade_name" class="form-label">Grade Level</label>
                            <input type="text" class="form-control" id="edit_grade_name" name="grade_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button
                            type="submit"
                            class="btn btn-primary" name="update_grade_level"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createSectionsModal" tabindex="-1" aria-labelledby="createSectionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="createSectionLabel">Add Grade Level & Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/<?= htmlspecialchars($role) ?>/SectionsController.php" method="post">
                    <?= Csrf::field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="grade_level_id" class="form-label">Grade Level</label>
                            <div class="d-flex gap-2">
                                <select name="grade_level_id" id="grade_level_id" class="form-select" required onchange="toggleNewGradeLevelInput(this, 'new_grade_name_wrapper')">
                                    <option value="" selected disabled>-- Choose Grade Level --</option>
                                    <?php foreach ($grade_levels as $grade_level): ?>
                                        <option value="<?= htmlspecialchars($grade_level['id']); ?>">
                                            <?= htmlspecialchars($grade_level['grade_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="__new__">+ Add New Grade Level</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" title="Rename selected grade level" onclick="renameSelectedGradeLevel('grade_level_id')">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Delete selected grade level" onclick="deleteSelectedGradeLevel('grade_level_id')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                            <div class="mt-2 d-none" id="new_grade_name_wrapper">
                                <input class="form-control" type="text" name="new_grade_name" id="new_grade_name" placeholder="e.g., Grade 7">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="section_name" class="form-label">Section Name</label>
                            <input
                                class="form-control"
                                type="text"
                                name="section_name"
                                id="section_name"
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

    <div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="editSectionLabel">Edit Grade Level & Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/<?= htmlspecialchars($role) ?>/SectionsController.php" method="post">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" id="edit_section_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_section_grade_level_id" class="form-label">Grade Level</label>
                            <div class="d-flex gap-2">
                                <select name="grade_level_id" id="edit_section_grade_level_id" class="form-select" required onchange="toggleNewGradeLevelInput(this, 'edit_new_grade_name_wrapper')">
                                    <option value="" selected disabled>-- Choose Grade Level --</option>
                                    <?php foreach ($grade_levels as $grade_level): ?>
                                        <option value="<?= htmlspecialchars($grade_level['id']); ?>">
                                            <?= htmlspecialchars($grade_level['grade_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="__new__">+ Add New Grade Level</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" title="Rename selected grade level" onclick="renameSelectedGradeLevel('edit_section_grade_level_id')">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Delete selected grade level" onclick="deleteSelectedGradeLevel('edit_section_grade_level_id')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                            <div class="mt-2 d-none" id="edit_new_grade_name_wrapper">
                                <input class="form-control" type="text" name="new_grade_name" id="edit_new_grade_name" placeholder="e.g., Grade 7">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_section_name" class="form-label">Section Name</label>
                            <input
                                class="form-control"
                                type="text"
                                name="section_name"
                                id="edit_section_name"
                            >
                        </div>
                        <div class="mb-3">
                            <label for="edit_adviser_id" class="form-label">Select Advisor / Teacher</label>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

    <div class="modal fade" id="teacherHistoryModal" tabindex="-1" aria-labelledby="teacherHistoryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="teacherHistoryLabel">Teacher Assignment History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>School Year</th>
                                    <th>Teacher</th>
                                    <th>Date Assigned</th>
                                </tr>
                            </thead>
                            <tbody id="teacherHistoryBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Per-section assignment history (section_teacher_assignments), newest
        // school year first — populated server-side so the History modal
        // needs no AJAX round-trip.
        window.sectionTeacherHistory = <?= json_encode($assignment_history ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>;
    </script>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Grade Levels & Section</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSectionsModal">Add Grade Level & Section</button>
        </div>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Assigned Teacher</th>
                        <th>Grade & Section</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($sections)): ?>
                        <?php foreach ($sections as $index => $section): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <select
                                        class="form-select form-select-sm"
                                        style="min-width: 200px;"
                                        data-section-id="<?= htmlspecialchars($section['id']); ?>"
                                        onchange="assignTeacherInline(this)"
                                    >
                                        <?php if(empty($section['adviser_id'])): ?>
                                            <option value="" selected>-- Unassigned --</option>
                                        <?php endif; ?>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?= htmlspecialchars($teacher['id']); ?>" <?= ((int) $teacher['id'] === (int) $section['adviser_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($teacher['full_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if(!empty($section['assigned_since'])): ?>
                                        <div class="form-text mb-0">Since <?= (new DateTime($section['assigned_since']))->format('M j, Y'); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($section['grade_level_name'] . ' - ' . $section['section_name'] ); ?></td>
                                <td><?= (new DateTime($section['created_at']))->format('F j, Y') ?></td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#teacherHistoryModal"
                                        onclick="showTeacherHistory('<?= htmlspecialchars($section['id']); ?>', '<?= htmlspecialchars($section['grade_level_name'] . ' - ' . $section['section_name']); ?>')"
                                        title="Teacher Assignment History"
                                    >
                                        <i class="bx bx-history"></i>
                                    </button>

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
                                        title="Edit"
                                    >
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <form action="../../../app/controllers/<?= htmlspecialchars($role) ?>/SectionsController.php" method="post" style="display: inline;">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($section['id']); ?>">
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            name="delete_section"
                                            title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this section? this action cannot be undone.')"
                                        >
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No Grade Levels & Sections Found, Create One.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once __DIR__ . "/../{$role}/partials/footer.php"; ?>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
    <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
    <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../../public/assets/vendor/js/menu.js"></script>
    <script src="../../../public/assets/js/main.js"></script>
    <script src="../../../public/js/shared/grade-level.js"></script>
    <script src="../../../public/js/shared/sections.js"></script>
</body>
</html>
