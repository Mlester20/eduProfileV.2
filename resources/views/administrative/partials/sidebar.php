<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">

    <!-- Menu -->
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
      <div class="app-brand demo">
        <a href="home.php" class="app-brand-link">
          <span class="app-brand-logo demo">
            <img src="../../../public/assets/img/favicon/logo.png" alt="logo" style="width: 50px; height: 50px;">
          </span>
          <span class="app-brand-text demo menu-text fw-bolder ms-2">EduProfile</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
          <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
      </div>

      <div class="menu-inner-shadow"></div>

      <ul class="menu-inner py-1">

        <!-- Dashboard -->
        <li class="menu-item <?php echo ($currentPage === 'home.php') ? 'active' : ''; ?>">
          <a href="home.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Dashboard">Dashboard</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Student Records</span>
        </li>

        <!-- Compiled Records -->
        <li class="menu-item <?php echo ($currentPage === 'compiled-records.php') ? 'active' : ''; ?>">
          <a href="compiled-records.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
            <div data-i18n="Compiled Records">Compiled Records</div>
          </a>
        </li>

        <!-- Learner Profile -->
        <li class="menu-item <?php echo ($currentPage === 'learner-profile.php') ? 'active' : ''; ?>">
          <a href="learner-profile.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-id-card"></i>
            <div data-i18n="Learner Profile">Learner Profile</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">School Year</span>
        </li>

        <!-- Student Rollover -->
        <li class="menu-item <?php echo ($currentPage === 'student-rollover.php') ? 'active' : ''; ?>">
          <a href="student-rollover.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-transfer"></i>
            <div data-i18n="Student Rollover">Student Rollover</div>
          </a>
        </li>

      </ul>
    </aside>
    <!-- / Menu -->

    <!-- Layout page -->
    <div class="layout-page">
