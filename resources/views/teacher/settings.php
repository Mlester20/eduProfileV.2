<?php
session_start();

require_once __DIR__ . '/../../../app/middleware/Auth.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/models/UpdateProfileModel.php';
require_once __DIR__ . '/../../../database/config/config.php';

AuthRole::allowOnly(['teacher']);

// Get user profile data
$updateProfileModel = new UpdateProfileModel($con);
$userProfile = $updateProfileModel->getUserById($_SESSION['id']);

// Format the Member Since date
$memberSince = (is_array($userProfile) && !empty($userProfile['created_at'])) ? date('M d, Y', strtotime($userProfile['created_at'])) : 'Unknown';

$roleBadgeClass = [
    'admin' => 'bg-label-primary',
    'administrative' => 'bg-label-warning',
    'teacher' => 'bg-label-success',
][$userProfile['role'] ?? ''] ?? 'bg-label-secondary';

// Resolve the current profile picture to a displayable src, without
// ever showing the raw storage path to the user.
$profileAvatarSrc = $userProfile['profile_picture'] ?? null;
$defaultAvatarSrc = '../../../public/assets/img/avatars/1.png';

if ($profileAvatarSrc) {
    $avatarRelPath = (strpos($profileAvatarSrc, '/') !== false) ? $profileAvatarSrc : ('storage/profiles/' . $profileAvatarSrc);
    $avatarFullServerPath = __DIR__ . '/../../../' . $avatarRelPath;
    $profileAvatarSrc = file_exists($avatarFullServerPath) ? ('../../../' . $avatarRelPath) : $defaultAvatarSrc;
} else {
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Profile Settings</title>
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
    <link rel="stylesheet" href="../../../public/assets/css/profile-settings.css" />
    <link rel="stylesheet" href="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../../public/assets/vendor/libs/apex-charts/apex-charts.css" />
    <script src="../../../public/assets/vendor/js/helpers.js"></script>
    <script src="../../../public/assets/js/config.js"></script>
</head>
<body>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <!-- Main content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Display flash messages -->
        <?php FlashMessage::showFlash(); ?>

        <!-- Profile header card -->
        <div class="card profile-header-card mb-4">
            <div class="profile-banner"></div>
            <div class="profile-header-body">
                <div class="profile-avatar-wrap">
                    <img id="profileAvatarImg" src="<?php echo htmlspecialchars($profileAvatarSrc); ?>" alt="Your profile photo" class="profile-avatar-img">
                    <label for="profilePicInput" class="profile-avatar-edit-btn" title="Change photo">
                        <i class='bx bx-camera'></i>
                    </label>
                    <span id="profileAvatarRemoveBtn" class="profile-avatar-remove-btn" style="display:none;" title="Cancel selection">
                        <i class='bx bx-x'></i>
                    </span>
                </div>
                <div class="profile-header-info">
                    <h5 class="mb-0"><?php echo htmlspecialchars($userProfile['full_name'] ?? 'N/A'); ?></h5>
                    <?php if(!empty($userProfile['position'])): ?>
                        <p class="text-muted mb-0"> Position: <?php echo htmlspecialchars($userProfile['position']); ?></p>
                    <?php endif; ?>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($userProfile['email'] ?? 'N/A'); ?></p>
                    <span class="badge profile-role-badge <?php echo $roleBadgeClass; ?>"><?php echo htmlspecialchars(ucfirst($userProfile['role'] ?? 'N/A')); ?></span>
                    <span class="text-muted small ms-2"><i class='bx bx-calendar'></i> Member since <?php echo htmlspecialchars($memberSince); ?></span>
                </div>
            </div>
            <div id="profileAvatarError" class="profile-avatar-error px-4 pb-3" style="display:none;"></div>
        </div>

        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="settings-section-title">Personal Information</p>
                        <p class="settings-section-help">Update your name, email, and profile photo.</p>

                        <form action="../../../app/controllers/UpdateProfile.php" method="POST" enctype="multipart/form-data" data-loading-submit>
                            <?= Csrf::field() ?>
                            <input type="hidden" name="redirect_role" value="teacher">
                            <input type="file" id="profilePicInput" name="profile_pic" accept=".jpg,.jpeg,.png,.gif" class="d-none">

                            <div class="mb-3">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullName" name="full_name" placeholder="e.g., Juan Dela Cruz"
                                       value="<?php echo htmlspecialchars($userProfile['full_name'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo htmlspecialchars($userProfile['email'] ?? ''); ?>" required>
                            </div>

                            <button type="submit" name="update_personal_info" value="1" class="btn btn-primary">Save Personal Information</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="settings-section-title">Security</p>
                        <p class="settings-section-help">Change your password. This requires your current password.</p>

                        <form action="../../../app/controllers/UpdateProfile.php" method="POST" data-loading-submit>
                            <?= Csrf::field() ?>
                            <input type="hidden" name="redirect_role" value="teacher">

                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password</label>
                                <div class="password-field-wrap">
                                    <input type="password" class="form-control" id="currentPassword" name="current_password"
                                           placeholder="Enter your current password" required>
                                    <button type="button" class="password-toggle-btn" data-target="currentPassword"><i class='bx bx-hide'></i></button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <div class="password-field-wrap">
                                    <input type="password" class="form-control" id="newPassword" name="new_password"
                                           placeholder="At least 8 characters" minlength="8" required>
                                    <button type="button" class="password-toggle-btn" data-target="newPassword"><i class='bx bx-hide'></i></button>
                                </div>
                                <div id="passwordStrengthWrap" class="password-strength-meter">
                                    <div id="passwordStrengthFill" class="password-strength-fill"></div>
                                </div>
                                <small id="passwordStrengthLabel" class="password-strength-label"></small>
                            </div>

                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <div class="password-field-wrap">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                                           placeholder="Re-enter new password" minlength="8" required>
                                    <button type="button" class="password-toggle-btn" data-target="confirmPassword"><i class='bx bx-hide'></i></button>
                                </div>
                                <small id="passwordMatchHint" class="password-match-hint"></small>
                            </div>

                            <button type="submit" name="update_security" value="1" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

    <!-- ── Vendor scripts ── -->
    <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
    <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
    <script src="../../../public/assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../../public/assets/vendor/js/menu.js"></script>
    <script src="../../../public/assets/js/main.js"></script>

    <!-- Profile Settings page script -->
    <script src="../../../public/js/shared/profile-settings.js"></script>
</body>
</html>
