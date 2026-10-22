<?php
// Get current page name
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">

    <!-- Menu -->
    <aside id="layout-menu" class="layout-menu menu-vertical menu sidebar-navy">
      <div class="app-brand demo">
        <a href="dashboard.php" class="app-brand-link">
          <span class="app-brand-logo demo">
            <img src="<?= base_url('public/assets/img/favicon/logo.png') ?>" alt="logo" style="width: 72px; height: 72px;">
          </span>
          <span class="app-brand-text demo menu-text fw-bolder ms-2"><span class="brand-accent">Edu</span>Profile</span>
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
        <!-- Grade Levels & Section -->
        <li class="menu-item <?php echo ($currentPage === 'grade-level.php' || $currentPage === 'sections.php') ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-note"></i>
            <div data-i18n="Grade Levels & Section">Grade Levels & Section</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="grade-level.php" class="menu-link">
                <div data-i18n="Error">Manage Grade Levels & Section</div>
              </a>
            </li>
          </ul>
        </li>

        <!-- Subjects -->
        <li class="menu-item <?php echo ($currentPage === 'subjects.php') ? 'active' : ''; ?>">
          <a href="subjects.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-book"></i>
            <div data-i18n="Subjects">Subjects</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">System</span>
        </li>

        <!-- School Settings -->
        <li class="menu-item <?php echo ($currentPage === 'school-settings.php') ? 'active' : ''; ?>">
          <a href="school-settings.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div data-i18n="School Settings">School Settings</div>
          </a>
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