<?php
require_once __DIR__ . '/../../../app/middleware/Auth.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/controllers/administrative/admindashboardcontroller.php';

AuthRole::allowOnly(['administrative']);

$dashboard = new AdminDashboardController($con);
$stats = $dashboard->getStats();
$recentActivity = $dashboard->getRecentActivity(8);

$moduleIcons = [
    'Students' => 'bx-user',
    'Parent/Guardian' => 'bx-group',
    'Academic Profile' => 'bx-book',
    'Achievement Profile' => 'bx-medal',
    'Student Behavioral' => 'bx-note',
    'Developmental' => 'bx-note',
    'Student Health' => 'bx-band-aid',
    'Attendance' => 'bx-calendar-check',
    'Section' => 'bx-building',
    'Section Teacher Assignment' => 'bx-user-check',
];
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Administrative Dashboard</title>
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

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <div>
          <h4 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Administrative'); ?></h4>
          <span class="text-muted">
              <?php if($stats['active_school_year']): ?>
                  School Year <?php echo htmlspecialchars($stats['active_school_year']['school_year']); ?>
              <?php else: ?>
                  No active school year set.
              <?php endif; ?>
          </span>
      </div>
  </div>

  <?php if(!empty($stats['sections_without_adviser'])): ?>
      <div class="alert alert-warning">
          <?php echo count($stats['sections_without_adviser']); ?> section(s) have no adviser assigned:
          <?php echo htmlspecialchars(implode(', ', array_map(function($s){ return $s['section_name']; }, $stats['sections_without_adviser']))); ?>.
      </div>
  <?php endif; ?>

  <!-- Stat cards -->
  <div class="row">
      <div class="col-md-3 col-sm-6 mb-4">
          <div class="card h-100">
              <div class="card-body d-flex justify-content-between align-items-start">
                  <div>
                      <span class="text-muted d-block mb-1">Active Learners</span>
                      <h3 class="mb-0"><?php echo (int) $stats['active_learners']; ?></h3>
                      <span class="text-muted small"><?php echo (int) $stats['archived_learners']; ?> archived</span>
                  </div>
                  <div class="avatar">
                      <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-user fs-4"></i></span>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4">
          <div class="card h-100">
              <div class="card-body d-flex justify-content-between align-items-start">
                  <div>
                      <span class="text-muted d-block mb-1">Sections</span>
                      <h3 class="mb-0"><?php echo (int) $stats['total_sections']; ?></h3>
                      <span class="text-muted small"><?php echo count($stats['sections_without_adviser']); ?> without adviser</span>
                  </div>
                  <div class="avatar">
                      <span class="avatar-initial rounded bg-label-info"><i class="bx bx-building fs-4"></i></span>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4">
          <div class="card h-100">
              <div class="card-body d-flex justify-content-between align-items-start">
                  <div>
                      <span class="text-muted d-block mb-1">Teachers</span>
                      <h3 class="mb-0"><?php echo (int) $stats['total_teachers']; ?></h3>
                  </div>
                  <div class="avatar">
                      <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-chalkboard fs-4"></i></span>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4">
          <div class="card h-100">
              <div class="card-body d-flex justify-content-between align-items-start">
                  <div>
                      <span class="text-muted d-block mb-1">Records This Year</span>
                      <h3 class="mb-0"><?php echo (int) array_sum($stats['records']); ?></h3>
                      <span class="text-muted small">across all categories</span>
                  </div>
                  <div class="avatar">
                      <span class="avatar-initial rounded bg-label-success"><i class="bx bx-spreadsheet fs-4"></i></span>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4">
          <div class="card h-100">
              <a href="at-risk.php" class="card-body d-flex justify-content-between align-items-start text-reset text-decoration-none">
                  <div>
                      <span class="text-muted d-block mb-1">At-Risk Learners</span>
                      <h3 class="mb-0"><?php echo (int) $stats['at_risk_count']; ?></h3>
                      <span class="text-muted small">this school year</span>
                  </div>
                  <div class="avatar">
                      <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-error-circle fs-4"></i></span>
                  </div>
              </a>
          </div>
      </div>
  </div>

  <div class="row">
      <!-- Records breakdown -->
      <div class="col-lg-4 mb-4">
          <div class="card h-100">
              <h5 class="card-header">Records This Year</h5>
              <div class="list-group list-group-flush">
                  <?php foreach($stats['records'] as $category => $count): ?>
                      <div class="list-group-item d-flex justify-content-between align-items-center">
                          <span><?php echo htmlspecialchars($category); ?></span>
                          <span class="badge bg-label-primary rounded-pill"><?php echo (int) $count; ?></span>
                      </div>
                  <?php endforeach; ?>
              </div>
          </div>
      </div>

      <!-- Quick links -->
      <div class="col-lg-4 mb-4">
          <div class="card h-100">
              <h5 class="card-header">Quick Links</h5>
              <div class="list-group list-group-flush">
                  <a href="learner-profile.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                      <i class="bx bx-id-card text-primary"></i> Learner Profile
                  </a>
                  <a href="compiled-records.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                      <i class="bx bx-spreadsheet text-primary"></i> Compiled Records
                  </a>
                  <a href="student-rollover.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                      <i class="bx bx-transfer text-primary"></i> Student Rollover
                  </a>
                  <a href="at-risk.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                      <i class="bx bx-error-circle text-primary"></i> At-Risk Learners
                  </a>
              </div>
          </div>
      </div>

      <!-- Recent activity -->
      <div class="col-lg-4 mb-4">
          <div class="card h-100">
              <h5 class="card-header">Recent Activity</h5>
              <div class="list-group list-group-flush" style="max-height: 420px; overflow-y: auto;">
                  <?php if(empty($recentActivity)): ?>
                      <div class="list-group-item text-muted">No recent activity yet.</div>
                  <?php else: ?>
                      <?php foreach($recentActivity as $log): ?>
                          <?php $icon = $moduleIcons[$log['module']] ?? 'bx-info-circle'; ?>
                          <div class="list-group-item d-flex align-items-start gap-3">
                              <span class="avatar avatar-sm">
                                  <span class="avatar-initial rounded-circle bg-label-secondary">
                                      <i class="bx <?php echo htmlspecialchars($icon); ?>"></i>
                                  </span>
                              </span>
                              <div class="flex-grow-1">
                                  <div><?php echo htmlspecialchars($log['actor_name'] ?? 'Unknown'); ?> &mdash; <?php echo htmlspecialchars($log['action']); ?></div>
                                  <div class="text-muted small"><?php echo htmlspecialchars(trim($log['description'] ?? '')); ?></div>
                                  <div class="text-muted small"><?php echo htmlspecialchars(date('M j, g:i A', strtotime($log['created_at']))); ?></div>
                              </div>
                          </div>
                      <?php endforeach; ?>
                  <?php endif; ?>
              </div>
          </div>
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
  <script src="../../../public/js/administrative/home.js"></script>
</body>
</html>
