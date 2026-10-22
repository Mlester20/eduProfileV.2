<?php
// Get current page name
$currentPage = basename($_SERVER['PHP_SELF']);

// Trash count badge -- queried directly here (rather than passed in by
// every single page's own controller) so it can never be forgotten when
// a new teacher page is added later.
$trashCount = 0;
if(isset($con) && $con instanceof mysqli && isset($_SESSION['id'])){
    $trashStmt = $con->prepare(
        "SELECT COUNT(*) AS total FROM students s
         LEFT JOIN sections sec ON s.section_id = sec.id
         WHERE sec.adviser_id = ? AND s.deleted_at IS NOT NULL"
    );
    $trashStmt->bind_param("i", $_SESSION['id']);
    $trashStmt->execute();
    $trashCount = (int) ($trashStmt->get_result()->fetch_assoc()['total'] ?? 0);
}
?>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar teacher-portal">
  <div class="layout-container">

    <!-- Menu -->
    <aside id="layout-menu" class="layout-menu menu-vertical menu sidebar-navy">
      <div class="app-brand demo">
        <a href="home.php" class="app-brand-link">
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
          <a href="students.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-dock-top"></i>
            <div data-i18n="Students">Students</div>
          </a>
        </li>

        <!-- Reports (dropdown: Overview, At-Risk Learners, Feeding Program, Class List) -->
        <?php $reportsPages = ['at-risk.php', 'feeding-program.php']; ?>
        <li class="menu-item <?php echo in_array($currentPage, $reportsPages, true) ? 'active' : ''; ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
            <div data-i18n="Reports">Reports</div>
          </a>
          <ul class="menu-sub">
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
            <!-- Class List -->
            <li class="menu-item <?php echo ($currentPage === 'class-list.php') ? 'active' : ''; ?>">
              <a href="class-list.php" class="menu-link">
                <div data-i18n="Class List">Class List</div>
              </a>
            </li>
          </ul>
        </li>

        <!-- Academic Records -->
        <li class="menu-item <?php echo ($currentPage === 'academic-records.php') ? 'active' : ''; ?>">
          <a href="academic-records.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-book-content"></i>
            <div data-i18n="Academic Records">Academic Records</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Trash</span>
        </li>

        <!-- Trash -->
        <li class="menu-item <?php echo ($currentPage === 'trash.php') ? 'active' : ''; ?>">
          <a href="trash.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-trash"></i>
            <div data-i18n="Trash">Trash</div>
            <?php if($trashCount > 0): ?>
              <div class="badge bg-label-danger rounded-pill ms-auto"><?php echo $trashCount; ?></div>
            <?php endif; ?>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">History</span>
        </li>

        <!-- Past Records -->
        <li class="menu-item <?php echo ($currentPage === 'past-records.php') ? 'active' : ''; ?>">
          <a href="past-records.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-history"></i>
            <div data-i18n="Past Records">Past Records</div>
          </a>
        </li>

        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Audit</span>
        </li>

        <!-- Audit Logs -->
        <li class="menu-item <?php echo ($currentPage === 'audit.php') ? 'active' : ''; ?>">
          <a href="audit.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-history"></i>
            <div data-i18n="Past Records">Audit Logs</div>
          </a>
        </li>
      </ul>
    </aside>
    <!-- / Menu -->

    <!-- Layout page -->
    <div class="layout-page">