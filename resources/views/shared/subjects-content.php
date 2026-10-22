<?php
require_once __DIR__ . "/../../../app/controllers/{$role}/SubjectsController.php";
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/helpers/Paginator.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly([$role]);

// Group subjects by grade level for display -- $subjects is already
// ordered by grade_levels.id then subject_name (see SubjectsModel::index()).
$subjectsByGrade = [];
foreach(($subjects ?: []) as $subject){
    $subjectsByGrade[$subject['grade_level_id']]['grade_name'] = $subject['grade_name'];
    $subjectsByGrade[$subject['grade_level_id']]['items'][] = $subject;
}
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Subjects</title>
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

    <div class="modal fade" id="createSubjectModal" tabindex="-1" aria-labelledby="createSubjectLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="createSubjectLabel">Add Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/<?= htmlspecialchars($role) ?>/SubjectsController.php" method="post">
                    <?= Csrf::field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="grade_level_id" class="form-label">Grade Level</label>
                            <select name="grade_level_id" id="grade_level_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Grade Level --</option>
                                <?php foreach ($grade_levels as $grade_level): ?>
                                    <option value="<?= htmlspecialchars($grade_level['id']); ?>">
                                        <?= htmlspecialchars($grade_level['grade_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input
                                class="form-control"
                                type="text"
                                name="subject_name"
                                id="subject_name"
                                placeholder="e.g., Filipino"
                                required
                            >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="create_subject">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="editSubjectLabel">Edit Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/<?= htmlspecialchars($role) ?>/SubjectsController.php" method="post">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" id="edit_subject_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_subject_grade_level_id" class="form-label">Grade Level</label>
                            <select name="grade_level_id" id="edit_subject_grade_level_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Grade Level --</option>
                                <?php foreach ($grade_levels as $grade_level): ?>
                                    <option value="<?= htmlspecialchars($grade_level['id']); ?>">
                                        <?= htmlspecialchars($grade_level['grade_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_subject_name" class="form-label">Subject Name</label>
                            <input class="form-control" type="text" name="subject_name" id="edit_subject_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_subject">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Subjects</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSubjectModal">Add Subject</button>
        </div>
        <div class="card-body py-2">
            <p class="text-muted mb-0 small">
                Subjects are scoped per grade level -- a teacher's Academic Records "Subject" dropdown only shows the subjects listed here for that student's grade level.
            </p>
        </div>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Grade Level</th>
                        <th>Subject</th>
                        <th>Recorded By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($subjectsByGrade)): ?>
                        <?php $rowIndex = 0; ?>
                        <?php foreach ($subjectsByGrade as $gradeGroup): ?>
                            <?php foreach ($gradeGroup['items'] as $subject): ?>
                                <?php $rowIndex++; ?>
                                <tr>
                                    <td><?= $rowIndex ?></td>
                                    <td><?= htmlspecialchars($gradeGroup['grade_name']) ?></td>
                                    <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($subject['recorded_by_name'] ?? '') ?></td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editSubjectModal"
                                            onclick="editSubject(
                                                '<?= htmlspecialchars($subject['id']); ?>',
                                                '<?= htmlspecialchars($subject['grade_level_id']); ?>',
                                                '<?= htmlspecialchars(addslashes($subject['subject_name'])); ?>'
                                            )"
                                            title="Edit"
                                        >
                                            <i class="bx bx-edit"></i>
                                        </button>

                                        <form action="../../../app/controllers/<?= htmlspecialchars($role) ?>/SubjectsController.php" method="post" style="display: inline;">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($subject['id']); ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                                name="delete_subject"
                                                title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this subject? this action cannot be undone.')"
                                            >
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No Subjects Found, Create One.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (($subjectsPaginated['total_pages'] ?? 1) > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $subjectsPaginated['current_page'] <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $subjectsPaginated['current_page'] - 1; ?>">&laquo;</a>
              </li>
              <?php foreach (Paginator::pageWindow($subjectsPaginated['current_page'], $subjectsPaginated['total_pages']) as $p): ?>
                <?php if ($p === null): ?>
                  <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                <?php else: ?>
                  <li class="page-item <?php echo $p === $subjectsPaginated['current_page'] ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
              <li class="page-item <?php echo $subjectsPaginated['current_page'] >= $subjectsPaginated['total_pages'] ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $subjectsPaginated['current_page'] + 1; ?>">&raquo;</a>
              </li>
            </ul>
          </nav>
        </div>
        <?php endif; ?>
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
    <script src="../../../public/js/shared/subjects.js"></script>
</body>
</html>
