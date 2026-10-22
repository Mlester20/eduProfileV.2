<?php
require_once __DIR__ . '/../../../app/controllers/administrative/teacherscontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/services/AddressService.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['administrative']);

$teacherId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$teacher = $teacherId ? $controller->show($teacherId) : null;

if(!$teacher){
    FlashMessage::setFlash('error', 'Teacher not found.');
    header('Location: teachers.php');
    exit();
}

$memberSince = !empty($teacher['created_at']) ? date('M d, Y', strtotime($teacher['created_at'])) : 'Unknown';
$birthDate = !empty($teacher['birth_date']) ? date('M d, Y', strtotime($teacher['birth_date'])) : 'N/A';
$teacherStatus = $teacher['status'] ?? 'active';
$statusBadge = $teacherStatus === 'archived' ? 'danger' : ($teacherStatus === 'active' ? 'success' : 'secondary');

$defaultAvatarSrc = '../../../public/assets/img/avatars/1.png';
$profileAvatarSrc = $teacher['profile_picture'] ?? null;
if($profileAvatarSrc){
    $avatarRelPath = (strpos($profileAvatarSrc, '/') !== false) ? $profileAvatarSrc : ('storage/profiles/' . $profileAvatarSrc);
    $avatarFullServerPath = __DIR__ . '/../../../' . $avatarRelPath;
    $profileAvatarSrc = file_exists($avatarFullServerPath) ? ('../../../' . $avatarRelPath) : $defaultAvatarSrc;
}else{
    $profileAvatarSrc = $defaultAvatarSrc;
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
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Teacher Profile</title>
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
    <link rel="stylesheet" href="../../../public/assets/css/theme-brown.css" />
    <link rel="stylesheet" href="../../../public/assets/css/profile-settings.css" />
    <link rel="stylesheet" href="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="../../../public/assets/vendor/js/helpers.js"></script>
    <script src="../../../public/assets/js/config.js"></script>
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center py-3 mb-4">
            <h4 class="mb-0"><span class="text-muted fw-light">Manage Teachers /</span> Teacher Profile</h4>
            <a href="teachers.php" class="btn btn-outline-secondary"><i class='bx bx-arrow-back'></i> Back to Manage Teachers</a>
        </div>

        <!-- Profile header card -->
        <div class="card profile-header-card mb-4">
            <div class="profile-banner"></div>
            <div class="profile-header-body">
                <div class="profile-avatar-wrap">
                    <img src="<?php echo htmlspecialchars($profileAvatarSrc); ?>" alt="Teacher's profile photo" class="profile-avatar-img">
                </div>
                <div class="profile-header-info">
                    <h5 class="mb-0"><?php echo htmlspecialchars($teacher['full_name'] ?? 'N/A'); ?></h5>
                    <?php if(!empty($teacher['position'])): ?>
                        <p class="text-muted mb-0 small"><?php echo htmlspecialchars($teacher['position']); ?></p>
                    <?php endif; ?>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($teacher['email'] ?? 'N/A'); ?></p>
                    <span class="badge profile-role-badge bg-label-success"><?php echo htmlspecialchars(ucfirst($teacher['role'] ?? 'Teacher')); ?></span>
                    <span class="badge bg-label-<?= $statusBadge ?>"><?php echo htmlspecialchars(ucfirst($teacherStatus)); ?></span>
                    <span class="text-muted small ms-2"><i class='bx bx-calendar'></i> Member since <?php echo htmlspecialchars($memberSince); ?></span>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card mb-4">
            <div class="card-body">
                <p class="settings-section-title">Account Information</p>
                <p class="settings-section-help">Read-only -- to make changes, use Edit from Manage Teachers.</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="d-block text-muted small mb-1">Contact Number</label>
                        <p class="mb-0 fw-medium"><?php echo htmlspecialchars($teacher['contact_number'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="d-block text-muted small mb-1">Sex</label>
                        <p class="mb-0 fw-medium"><?php echo htmlspecialchars($teacher['sex'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="d-block text-muted small mb-1">Birth Date</label>
                        <p class="mb-0 fw-medium"><?php echo htmlspecialchars($birthDate); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="d-block text-muted small mb-1">Address</label>
                        <p class="mb-0 fw-medium"><?php echo htmlspecialchars(AddressService::formatFullAddress($teacher)) ?: 'N/A'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

    <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
    <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
    <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../../public/assets/vendor/js/menu.js"></script>
    <script src="../../../public/assets/js/main.js"></script>
</body>
</html>
