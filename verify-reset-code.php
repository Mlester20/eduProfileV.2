<?php
session_start();

require_once __DIR__ . '/app/helpers/flashMessage.php';
require_once __DIR__ . '/app/helpers/csrf.php';
require_once __DIR__ . '/app/core/BaseUrl.php';
require_once __DIR__ . '/app/helpers/SchoolSettings.php';
require_once __DIR__ . '/database/config/config.php';

if(empty($_SESSION['pwreset_email'])){
    FlashMessage::setFlash('warning', 'Please request a verification code first.');
    header('Location: ' . base_url('forgot-password.php'));
    exit();
}

$school = SchoolSettings::get($con);
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-public="public/assets/"
  data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <title><?php require_once __DIR__ . '/app/helpers/title.php'; ?> | Verify Code</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="<?= base_url('public/assets/img/favicon/logo.png') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= base_url('public/assets/vendor/fonts/boxicons.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/vendor/css/core.css') ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= base_url('public/assets/vendor/css/theme-default.css') ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= base_url('public/assets/css/demo.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/css/theme-brown.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/vendor/css/pages/page-auth.css') ?>?v=<?php echo filemtime(__DIR__ . '/public/assets/vendor/css/pages/page-auth.css'); ?>" />
    <script src="<?= base_url('public/assets/vendor/js/helpers.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/config.js') ?>"></script>
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <div class="auth-split-wrapper">

      <!-- Left brand panel -->
      <div class="auth-left-panel">
        <div class="auth-illustration">
          <div class="illus-blob"></div>
          <div class="illus-card">
            <img src="<?= base_url('public/assets/img/favicon/logo.png') ?>" alt="EduProfile" class="illus-logo" />
          </div>
          <div class="illus-badge"><i class='bx bx-mail-send'></i></div>
        </div>

        <div>
          <p class="auth-left-title">EduProfile</p>
          <p class="auth-left-tagline">An Innovative Student Profiling System for Learners of San Jose Sur Elementary School</p>
          <p class="auth-left-subtext"><?php echo htmlspecialchars($school['school_name']); ?> &nbsp;•&nbsp; <?php echo htmlspecialchars($school['district']); ?> &nbsp;•&nbsp; DepEd <?php echo htmlspecialchars($school['region']); ?></p>
        </div>
      </div>

      <!-- Right auth form panel -->
      <div class="auth-right-panel">
        <div class="auth-card">

          <p class="auth-desc">Enter the 6-digit verification code we sent to <strong><?= htmlspecialchars($_SESSION['pwreset_email']) ?></strong>.</p>
          <hr class="auth-divider" />

          <form id="formVerifyCode" class="mb-3" action="<?= base_url('app/controllers/VerifyResetCode.php') ?>" method="POST">
            <?= Csrf::field() ?>

            <div class="mb-3">
              <label for="code" class="auth-label">Verification Code</label>
              <div class="input-icon-wrap">
                <span class="input-icon"><i class='bx bx-shield-quarter'></i></span>
                <input
                  type="text"
                  class="form-control auth-input"
                  id="code"
                  name="code"
                  placeholder="123456"
                  inputmode="numeric"
                  pattern="[0-9]{6}"
                  maxlength="6"
                  autofocus
                  required
                />
              </div>
            </div>

            <div class="mb-3">
              <button class="btn auth-btn w-100" type="submit">Verify Code</button>
            </div>

          </form>

          <form id="formResendCode" action="<?= base_url('app/controllers/ForgotPassword.php') ?>" method="POST" class="mb-3">
            <?= Csrf::field() ?>
            <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['pwreset_email']) ?>" />
            <button type="submit" class="btn btn-link w-100 p-0"><i class='bx bx-time-five'></i> Didn't get a code? Resend</button>
          </form>

          <div class="text-center">
            <a href="<?= base_url('forgot-password.php') ?>"><i class='bx bx-arrow-back'></i> Use a different email</a>
          </div>

        </div>
      </div>

    </div>

    <script src="<?= base_url('public/assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('public/assets/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= base_url('public/assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
    <script src="<?= base_url('public/assets/vendor/js/menu.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/main.js') ?>"></script>
</body>
</html>
