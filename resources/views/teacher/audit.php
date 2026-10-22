<?php
session_start();
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/models/teacher/AuditLogsModel.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

try{
    $user_id = $_SESSION['id'];
    $audit_logs = new AuditLogsModel($con);
    $logs = $audit_logs->getAudit($user_id);
}catch(Exception $e){
    error_log("Something went wrong while trying to get the audit logs");
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

    <div class="card">
        <h5 class="card-header">Audit Logs</h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Action</th> 
                        <th>Module</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($logs)): ?>
                        <?php foreach($logs as $index => $log): ?>
                        <tr>
                            <td> <?php echo $index + 1; ?> </td>
                            <td> <?php echo htmlspecialchars($log['action']); ?> </td>
                            <td> <?php echo htmlspecialchars($log['module']); ?> </td>
                            <td> <?php echo htmlspecialchars($log['description']); ?> </td>
                            <td> <?php echo htmlspecialchars($log['status']); ?> </td>
                            <td> <?php echo htmlspecialchars($log['created_at']); ?> </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="text-center" colspan="7">No Audit Logs Found To Your Account</td>
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
  <script src="../../../public/js/teacher/home.js"></script>
</body>
</html>
