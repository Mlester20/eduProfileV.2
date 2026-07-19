<?php
require_once __DIR__ . '/../../../app/controllers/admin/AuditLogController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['admin']);

$auditRows = $result['data'] ?? [];
$auditPage = $result['current_page'] ?? 1;
$auditPages = $result['total_pages'] ?? 1;
$auditTotal = $result['total'] ?? 0;

$filterQuery = http_build_query(array_filter([
    'module' => $filters['module'] ?? '',
    'role' => $filters['role'] ?? '',
    'date_from' => $filters['date_from'] ?? '',
    'date_to' => $filters['date_to'] ?? '',
    'search' => $filters['search'] ?? '',
]));
$filterQuery = $filterQuery ? $filterQuery . '&' : '';
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | System Audit Log</title>
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

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h4 class="mb-0">System Audit Log</h4>
        <span class="text-muted"><?php echo (int) $auditTotal; ?> total entries</span>
    </div>

    <form action="audit-log.php" method="get" class="row g-2 align-items-end mb-3">
        <div class="col-md-2">
            <label for="module" class="form-label mb-0">Module</label>
            <select class="form-select" id="module" name="module">
                <option value="">-- All Modules --</option>
                <?php foreach(($modules ?? []) as $mod): ?>
                    <option value="<?php echo htmlspecialchars($mod); ?>" <?php echo ($filters['module'] === $mod) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mod); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="role" class="form-label mb-0">Role</label>
            <select class="form-select" id="role" name="role">
                <option value="">-- All Roles --</option>
                <?php foreach(['admin', 'administrative', 'teacher'] as $r): ?>
                    <option value="<?php echo htmlspecialchars($r); ?>" <?php echo ($filters['role'] === $r) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(ucfirst($r)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="date_from" class="form-label mb-0">Date From</label>
            <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label mb-0">Date To</label>
            <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <label for="search" class="form-label mb-0">Search</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Action, description, or user name" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
        </div>
        <div class="col-md-1 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-filter-alt"></i></button>
        </div>
        <?php if(!empty(array_filter($filters))): ?>
            <div class="col-12">
                <a href="audit-log.php" class="btn btn-sm btn-outline-secondary">Clear filters</a>
            </div>
        <?php endif; ?>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($auditRows as $log): ?>
                        <tr>
                            <td class="text-nowrap"><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($log['created_at']))); ?></td>
                            <td><?php echo htmlspecialchars($log['actor_name'] ?? 'Unknown'); ?></td>
                            <td><span class="badge bg-label-secondary"><?php echo htmlspecialchars(ucfirst($log['role'] ?? '')); ?></span></td>
                            <td><?php echo htmlspecialchars($log['action'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['module'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['description'] ?? ''); ?></td>
                            <td>
                                <span class="badge bg-label-<?php echo ($log['status'] ?? 'success') === 'failed' ? 'danger' : 'success'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($log['status'] ?? 'success')); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($auditRows)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No audit log entries match the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($auditPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $auditPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $filterQuery; ?>page=<?php echo $auditPage - 1; ?>">&laquo;</a>
              </li>
              <?php for($p = 1; $p <= $auditPages; $p++): ?>
                <li class="page-item <?php echo $p === $auditPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?<?php echo $filterQuery; ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $auditPage >= $auditPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $filterQuery; ?>page=<?php echo $auditPage + 1; ?>">&raquo;</a>
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
</body>
</html>
