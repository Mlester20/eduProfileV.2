<?php
$profilePic = $_SESSION['profile_picture'] ?? null;
if ($profilePic) {
    if (strpos($profilePic, '/') !== false) {
        $imgSrc = '../../../' . $profilePic;
    } else {
        $imgSrc = '../../../storage/profiles/' . $profilePic;
    }
} else {
    $imgSrc = '../../../public/assets/img/avatars/8.jpg';
}
?>

<!-- Navbar -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Portal Label -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-chalkboard fs-4 lh-0 me-2 text-muted"></i>
        <span class="text-muted small fw-semibold">Teacher Portal</span>
      </div>
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- User Dropdown -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="<?php echo $imgSrc; ?>" alt class="w-px-40 h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="<?php echo $imgSrc; ?>" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                  <small class="text-muted"><?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></small>
                </div>
              </div>
            </a>
          </li>
          <li><div class="dropdown-divider"></div></li>
          <li>
            <a class="dropdown-item" href="settings.php">
              <i class="bx bx-cog me-2"></i>
              <span class="align-middle">Profile</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="../../../app/controllers/Logout.php"
               onclick="return confirm('Are you sure you want to logout?')">
              <i class="bx bx-power-off me-2"></i>
              <span class="align-middle">Log Out</span>
            </a>
          </li>
        </ul>
      </li>
      <!--/ User Dropdown -->
    </ul>
  </div>
</nav>
<!-- / Navbar -->

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
