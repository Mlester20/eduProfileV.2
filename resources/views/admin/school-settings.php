<?php
require_once __DIR__ . '/../../../app/controllers/admin/SchoolSettingsController.php';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | School Settings</title>
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

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">System /</span> School Settings</h4>
        <p class="text-muted">School identity used across sign-in, printed forms (SF1/SF2), and compiled reports — edit it here instead of it being hardcoded per page.</p>

        <?php FlashMessage::showFlash(); ?>

        <div class="row">
            <!-- School Logo -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-3">School Logo</h5>

                        <img src="../../../public/assets/img/favicon/logo.png?v=<?php echo filemtime(__DIR__ . '/../../../public/assets/img/favicon/logo.png'); ?>"
                             alt="School Logo" class="rounded mb-3" width="120" height="120" style="object-fit: contain;">

                        <form action="../../../app/controllers/admin/SchoolSettingsController.php" method="POST" enctype="multipart/form-data" class="text-start">
                            <?= Csrf::field() ?>
                            <div class="mb-3">
                                <label for="school_logo" class="form-label">Replace Logo</label>
                                <input type="file" class="form-control" id="school_logo" name="school_logo" accept=".jpg,.jpeg,.png,.gif">
                                <small class="text-muted d-block mt-2">JPG, PNG, or GIF (Max 2MB). Used app-wide — sign-in page, sidebar, printed forms.</small>
                            </div>
                            <button type="submit" name="upload_school_logo" class="btn btn-outline-primary w-100">Upload New Logo</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- School Identity -->
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">School Identity</h5>

                        <form action="../../../app/controllers/admin/SchoolSettingsController.php" method="POST">
                            <?= Csrf::field() ?>

                            <div class="mb-3">
                                <label for="school_name" class="form-label">School Name</label>
                                <input type="text" class="form-control" id="school_name" name="school_name"
                                       placeholder="e.g., San Jose Sur Elementary"
                                       value="<?php echo htmlspecialchars($school_settings['school_name'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="school_id" class="form-label">School ID</label>
                                <input type="text" class="form-control" id="school_id" name="school_id"
                                       placeholder="e.g., 103503"
                                       value="<?php echo htmlspecialchars($school_settings['school_id'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="region" class="form-label">Region</label>
                                <input type="text" class="form-control" id="region" name="region"
                                       placeholder="e.g., Region II"
                                       value="<?php echo htmlspecialchars($school_settings['region'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="division" class="form-label">Division</label>
                                <input type="text" class="form-control" id="division" name="division"
                                       placeholder="e.g., Division of Isabela"
                                       value="<?php echo htmlspecialchars($school_settings['division'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="district" class="form-label">District</label>
                                <input type="text" class="form-control" id="district" name="district"
                                       placeholder="e.g., District of Mallig"
                                       value="<?php echo htmlspecialchars($school_settings['district'] ?? ''); ?>">
                            </div>

                            <button type="submit" name="update_school_settings" class="btn btn-primary">Save Changes</button>
                        </form>
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
