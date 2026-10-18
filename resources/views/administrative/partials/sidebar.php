<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">

    <!-- Menu -->
    <aside id="layout-menu" class="layout-menu menu-vertical menu sidebar-navy">
      <div class="app-brand demo">
        <a href="home.php" class="app-brand-link">
          <span class="app-brand-logo demo">
            <img src="<?= base_url('public/assets/img/favicon/logo.png') ?>" alt="logo" style="width: 60px; height: 60px;">
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
        <li class="menu-item <?php echo ($currentPage === 'home.php') ? 'active' : ''; ?>">
          <a href="home.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Dashboard">Dashboard</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Student Records</span>
        </li>

        <!-- Records (dropdown: Compiled Records, Learner's Profile, Health Profile) -->
        <?php $recordsPages = ['compiled-records.php', 'learner-profile.php', 'health-profile.php']; ?>
        <li class="menu-item <?php echo in_array($currentPage, $recordsPages, true) ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
            <div data-i18n="Records">Records</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item <?php echo ($currentPage === 'compiled-records.php') ? 'active' : ''; ?>">
              <a href="compiled-records.php" class="menu-link">
                <div data-i18n="Compiled Records">Compiled Records</div>
              </a>
            </li>
            <li class="menu-item <?php echo ($currentPage === 'learner-profile.php') ? 'active' : ''; ?>">
              <a href="learner-profile.php" class="menu-link">
                <div data-i18n="Learner's Profile">Learner's Profile</div>
              </a>
            </li>
            <li class="menu-item <?php echo ($currentPage === 'health-profile.php') ? 'active' : ''; ?>">
              <a href="health-profile.php" class="menu-link">
                <div data-i18n="Health Profile">Health Profile</div>
              </a>
            </li>
          </ul>
        </li>

        <!-- Reports (dropdown: Overview, At-Risk Learners, Feeding Program, Class List) -->
        <?php $reportsPages = ['reports.php', 'at-risk.php', 'feeding-program.php', 'class-list.php']; ?>
        <li class="menu-item <?php echo in_array($currentPage, $reportsPages, true) ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
            <div data-i18n="Reports">Reports</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item <?php echo ($currentPage === 'reports.php') ? 'active' : ''; ?>">
              <a href="reports.php" class="menu-link">
                <div data-i18n="Overview">Overview</div>
              </a>
            </li>
            <li class="menu-item <?php echo ($currentPage === 'at-risk.php') ? 'active' : ''; ?>">
              <a href="at-risk.php" class="menu-link">
                <div data-i18n="At-Risk Learners">At-Risk Learners</div>
              </a>
            </li>
            <li class="menu-item <?php echo ($currentPage === 'feeding-program.php') ? 'active' : ''; ?>">
              <a href="feeding-program.php" class="menu-link">
                <div data-i18n="Feeding Program">Feeding Program</div>
              </a>
            </li>
            <li class="menu-item <?php echo ($currentPage === 'class-list.php') ? 'active' : ''; ?>">
              <a href="class-list.php" class="menu-link">
                <div data-i18n="Class List">Class List</div>
              </a>
            </li>
          </ul>
        </li>

        <!-- Possible Duplicates -->
        <li class="menu-item <?php echo ($currentPage === 'duplicates.php') ? 'active' : ''; ?>">
          <a href="duplicates.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-copy-alt"></i>
            <div data-i18n="Possible Duplicates">Possible Duplicates</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Academic Setup</span>
        </li>

        <!-- Grade Levels & Section -->
        <li class="menu-item <?php echo ($currentPage === 'grade-level.php' || $currentPage === 'sections.php') ? 'active' : ''; ?>">
          <a href="grade-level.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-note"></i>
            <div data-i18n="Grade Levels & Section">Grade Levels & Section</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Staff</span>
        </li>

        <!-- Manage Teachers -->
        <li class="menu-item <?php echo ($currentPage === 'teachers.php') ? 'active' : ''; ?>">
          <a href="teachers.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-chalkboard"></i>
            <div data-i18n="Manage Teachers">Manage Teachers</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">School Year</span>
        </li>

        <!-- Manage School Year -->
        <li class="menu-item <?php echo ($currentPage === 'sy.php') ? 'active' : ''; ?>">
          <a href="sy.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-calendar"></i>
            <div data-i18n="School Year">Manage School Year</div>
          </a>
        </li>

        <!-- Student Rollover -->
        <li class="menu-item <?php echo ($currentPage === 'student-rollover.php') ? 'active' : ''; ?>">
          <a href="student-rollover.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-transfer"></i>
            <div data-i18n="Student Rollover">Student Rollover</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Logs</span>
        </li>

        <!-- Manage School Year -->
        <li class="menu-item <?php echo ($currentPage === 'activity-logs.php') ? 'active' : ''; ?>">
          <a href="activity-logs.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-history"></i>
            <div data-i18n="School Year">Activity Logs</div>
          </a>
        </li>

      </ul>
    </aside>
    <!-- / Menu -->

    <!-- Layout page -->
    <div class="layout-page">
