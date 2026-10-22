<?php
require_once __DIR__ . '/../../../app/controllers/teacher/TrashController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/helpers/Paginator.php';
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
  <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Trash</title>
  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../../../public/assets/img/favicon/logo.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../../public/assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../../../public/assets/css/demo.css" />
  <link rel="stylesheet" href="../../../public/assets/css/theme-brown.css" />
  <link rel="stylesheet" href="../../../public/assets/css/teacher-portal.css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <script src="../../../public/assets/vendor/js/helpers.js"></script>
  <script src="../../../public/assets/js/config.js"></script>
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h5 class="mb-0">Trash</h5>
          <p class="text-muted small mb-0">Students here are hidden everywhere else. They're permanently deleted automatically after <?php echo (int) \StudentScope::retentionDays(); ?> days.</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <div class="position-relative" style="max-width: 280px; width: 100%;">
                <input
                    type="text"
                    class="form-control"
                    id="trash_search"
                    placeholder="Search by name or LRN..."
                    autocomplete="off"
                    value="<?php echo htmlspecialchars($trash_search ?? ''); ?>"
                >
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-success" id="bulkRestoreBtn" disabled>
                    <i class="bx bx-undo"></i> Restore Selected
                </button>
                <button type="button" class="btn btn-outline-danger" id="bulkDeleteBtn" disabled>
                    <i class="bx bx-trash"></i> Delete Selected
                </button>
            </div>
            <button type="button" class="btn btn-danger" id="emptyTrashBtn">
                <i class="bx bx-trash-alt"></i> Empty Trash
            </button>
        </div>
      </div>
        <div class="table-responsive nowrap">
          <table class="table">
            <thead>
              <tr>
                <th style="width: 2rem;"><input type="checkbox" class="form-check-input" id="selectAllCheckbox"></th>
                <th>LRN</th>
                <th>Student Name</th>
                <th>Grade & Section</th>
                <th>School Year</th>
                <th>Deleted By</th>
                <th>Deleted On</th>
                <th>Days Left</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $rows        = $trashPaginated['data']         ?? [];
                $currentPage = $trashPaginated['current_page'] ?? 1;
                $totalPages  = $trashPaginated['total_pages']  ?? 1;
              ?>
              <?php if (empty($rows)): ?>
                <tr>
                  <td colspan="9" class="text-center py-5">
                    <i class='bx bx-trash fs-1 text-muted d-block mb-2'></i>
                    <p class="text-muted mb-0"><?php echo $trash_search ? 'No trashed students match your search.' : "Trash is empty — deleted students will show up here."; ?></p>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($rows as $row): ?>
                  <?php
                    $fullName = trim($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name'] . ' ' . ($row['suffix'] ?? ''));
                    $daysLeft = (int) $row['days_left'];
                  ?>
                  <tr>
                    <td><input type="checkbox" class="form-check-input trash-row-checkbox" value="<?php echo (int) $row['id']; ?>"></td>
                    <td><?php echo htmlspecialchars($row['lrn']); ?></td>
                    <td><?php echo htmlspecialchars($fullName); ?></td>
                    <td><?php echo htmlspecialchars(($row['grade_name'] ?? '') . ' - ' . ($row['section_name'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars($row['school_year'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['deleted_by_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars((new DateTime($row['deleted_at']))->format('M j, Y g:i A')); ?></td>
                    <td><span class="badge <?php echo $daysLeft <= 5 ? 'bg-label-danger' : 'bg-label-warning'; ?>"><?php echo $daysLeft; ?> day<?php echo $daysLeft === 1 ? '' : 's'; ?></span></td>
                    <td>
                      <button
                        class="btn btn-sm btn-success"
                        type="button"
                        title="Restore"
                        data-bs-toggle="tooltip"
                        onclick="restoreStudent(<?php echo (int) $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($fullName)); ?>')"
                      >
                        <i class="bx bx-undo"></i>
                      </button>
                      <button
                        class="btn btn-sm btn-danger"
                        type="button"
                        title="Delete Permanently"
                        data-bs-toggle="tooltip"
                        onclick="permanentDeleteStudent(<?php echo (int) $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($fullName)); ?>', '<?php echo htmlspecialchars(addslashes($row['lrn'])); ?>')"
                      >
                        <i class="bx bx-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php $trashQueryBase = $trash_search ? ('search=' . urlencode($trash_search) . '&') : ''; ?>
        <?php if ($totalPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $trashQueryBase; ?>page=<?php echo $currentPage - 1; ?>">&laquo;</a>
              </li>
              <?php foreach (Paginator::pageWindow($currentPage, $totalPages) as $p): ?>
                <?php if ($p === null): ?>
                  <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                <?php else: ?>
                  <li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo $trashQueryBase; ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
              <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $trashQueryBase; ?>page=<?php echo $currentPage + 1; ?>">&raquo;</a>
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
    <script src="../../../public/js/teacher/modal-helpers.js?v=<?php echo filemtime(__DIR__ . '/../../../public/js/teacher/modal-helpers.js'); ?>"></script>
    <script src="../../../public/js/teacher/trash.js?v=<?php echo filemtime(__DIR__ . '/../../../public/js/teacher/trash.js'); ?>"></script>
</body>
</html>
