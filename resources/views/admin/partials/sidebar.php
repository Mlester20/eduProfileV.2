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
        <a href="dashboard.php" class="app-brand-link">
          <span class="app-brand-logo demo">
            <img src="<?= base_url('public/assets/img/favicon/logo.png') ?>" alt="logo" style="width: 50px; height: 50px;">
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
        <li class="menu-item <?php echo ($currentPage === 'dashboard.php') ? 'active' : ''; ?>">
          <a href="dashboard.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Lists</span>
        </li>

        <!-- Users Management -->
        <li class="menu-item <?php echo ($currentPage === 'users.php') ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-dock-top"></i>
            <div data-i18n="Users Management">Users Management</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="users.php" class="menu-link">
                <div data-i18n="Account">Manage Users</div>
              </a>
            </li>
          </ul>
        </li>

        <!-- School Year -->
        <li class="menu-item <?php echo ($currentPage === 'sy.php' || $currentPage === 'pages-misc-under-maintenance.html') ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-calendar"></i>
            <div data-i18n="Misc">School Year</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="sy.php" class="menu-link">
                <div data-i18n="Error">Manage School Year</div>
              </a>
            </li>
          </ul>
        </li>
        <!-- Grade Level -->
        <li class="menu-item <?php echo ($currentPage === 'grade-level.php' || $currentPage === 'sections.php') ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-note"></i>
            <div data-i18n="Grade Level">Grade Levels</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="grade-level.php" class="menu-link">
                <div data-i18n="Error">Manage Grade Level</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="sections.php" class="menu-link">
                <div data-i18n="Error">Manage Sections</div>
              </a>
            </li>
          </ul>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">System</span>
        </li>

        <!-- Audit Log -->
        <li class="menu-item <?php echo ($currentPage === 'audit-log.php') ? 'active' : ''; ?>">
          <a href="audit-log.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-history"></i>
            <div data-i18n="Audit Log">System Audit Log</div>
          </a>
        </li>
      </ul>
    </aside>
    <!-- / Menu -->

    <!-- Layout page -->
    <div class="layout-page">