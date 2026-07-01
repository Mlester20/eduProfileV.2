<?php
require_once __DIR__ . '/../../../app/controllers/teacher/ParentGuardianController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Parent & Guardian</title>
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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addParentModal">
            Add Parent/Guardian
        </button>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">Parent & Guardian Information</h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Guardian Name</th>
                        <th>Contact Number</th>
                        <th>Relationship</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($parentGuardians)) : ?>
                        <?php foreach ($parentGuardians as $index => $parentGuardian) : ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($parentGuardian['student_first_name'] . ' ' . $parentGuardian['student_middle_name'] . ' ' . $parentGuardian['student_last_name'] . ' ' . $parentGuardian['student_suffix']) ?></td>
                                <td><?= htmlspecialchars($parentGuardian['guardian_name']) ?></td>
                                <td><?= htmlspecialchars($parentGuardian['guardian_contact']) ?></td>
                                <td><?= htmlspecialchars($parentGuardian['guardian_relationship']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">No parent/guardian records found.</td>
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
