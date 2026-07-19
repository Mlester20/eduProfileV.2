<?php
require_once __DIR__ . '/../../../app/controllers/admin/SchoolYearController.php';
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
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <title> <?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | School Year </title>
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
    <link rel="stylesheet" href="../../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../../public/assets/vendor/libs/apex-charts/apex-charts.css" />
    <script src="../../../public/assets/vendor/js/helpers.js"></script>
    <script src="../../../public/assets/js/config.js"></script>
  </head>
<body>

    <?php FlashMessage::showFlash(); ?>
   
    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <div class="text-end">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSyModal">Add School Year</button>
    </div>

    <!-- add sy modal -->
    <div class="modal fade" id="createSyModal" tabindex="-1" aria-labelledby="createSyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSyModalLabel">Add School Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/admin/SchoolYearController.php" method="POST">
                    <?= Csrf::field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="school_year" class="form-label">School Year</label>
                            <input type="text" class="form-control" id="school_year"  
                            placeholder="e.g., 2025-2026"
                            name="school_year" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="create_sy">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- edit sy modal -->
    <div class="modal fade" id="editSyModal" tabindex="-1" aria-labelledby="editSyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSyModalLabel">Edit School Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/admin/SchoolYearController.php" method="POST">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" id="edit_sy_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_school_year" class="form-label">School Year</label>
                            <input type="text" class="form-control" id="edit_school_year" name="school_year" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_sy">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-4">
      <h5 class="card-header">Manage School Year</h5>
      <div class="table-responsive nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>School Year</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($school_years as $sy): ?>
                <tr>
                  <td><?= htmlspecialchars($sy['id']); ?></td>
                  <td><?= htmlspecialchars($sy['school_year']); ?></td>
                  <td><?= htmlspecialchars($sy['start_date']); ?></td>
                  <td><?= htmlspecialchars($sy['end_date']); ?> </td>
                  <td>
                      <?php
                          $status = strtolower($sy['status']);

                          $badgeClass = match($status){
                              'active' => 'bg-label-success',
                              'inactive' => 'bg-label-warning',
                              'archived' => 'bg-label-danger',
                              default => 'bg-label-secondary'
                          };
                      ?>

                      <span class="badge <?= $badgeClass; ?>">
                          <?= htmlspecialchars($sy['status']); ?>
                      </span>
                  </td>
                  <td>
                    <button 
                      class="btn btn-warning btn-sm" 
                      data-bs-toggle="modal" 
                      data-bs-target="#editSyModal"
                      onclick="editSy(
                        '<?= htmlspecialchars($sy['id']); ?>', 
                        '<?= htmlspecialchars($sy['school_year']); ?>', 
                        '<?= htmlspecialchars($sy['start_date']); ?>', 
                        '<?= htmlspecialchars($sy['end_date']); ?>', 
                        '<?= htmlspecialchars($sy['status']); ?>'
                      )"
                    >
                      Edit
                    </button>
                    <form action="../../../app/controllers/admin/SchoolYearController.php" method="POST" style="display:inline-block;">
                        <?= Csrf::field() ?>
                      <input type="hidden" name="id" value="<?= htmlspecialchars($sy['id']); ?>">
                      <button 
                          type="submit" 
                          name="delete_sy" 
                          class="btn btn-danger btn-sm"
                          onclick="return confirm('are you sure you want to delete this record?')"  
                        >
                        Delete
                      </button>
                    </form>
                  </td>
                </tr>
            <?php endforeach; ?>
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
    <script src="../../../public/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../../../public/assets/js/main.js"></script>
    <script src="../../../public/assets/js/dashboards-analytics.js"></script>
    <script src="../../../public/js/admin/sy.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>