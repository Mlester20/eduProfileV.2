<?php
// Get current page name
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
            <img src="../../../../public/assets/img/favicon/logo.png" alt="logo" style="width: 50px; height: 50px;">
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
            <div data-i18n="Analytics">Dashboard</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Lists</span>
        </li>

        <!-- Students Information Management -->
        <li class="menu-item <?php echo ($currentPage === 'students.php') ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-dock-top"></i>
            <div data-i18n="Students Management">Students</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="students.php" class="menu-link">
                <div data-i18n="Account">Manage Students</div>
              </a>
            </li>
          </ul>
        </li>

        <!-- Parent/Guardian -->
        <li class="menu-item <?php echo ($currentPage === 'parent-guardian.php' || $currentPage === 'pages-misc-under-maintenance.html') ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div data-i18n="Parent/Guardian">Parent/Guardian</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="parent-guardian.php" class="menu-link">
                <div data-i18n="Error">Manage Parent/Guardian</div>
              </a>
            </li>
          </ul>
        </li>

        <!-- Observations -->
        <li class="menu-item <?php echo ($currentPage === 'student-behavior.php') ? 'active' : ''; ?>">
          <a href="student-behavior.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-note"></i>
            <div data-i18n="Observations">Student Behavior</div>
          </a>
        </li>

      </ul>
    </aside>
    <!-- / Menu -->

    <!-- Layout page -->
    <div class="layout-page">