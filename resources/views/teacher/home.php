<?php
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
require_once __DIR__ . '/../../../app/controllers/teacher/DashboardController.php';
AuthRole::allowOnly(['teacher']);

$dashboard = new DashboardController($con);
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Teacher Dashboard</title>
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

  <?php if($stats === null): ?>
    <div class="alert alert-warning">Unable to load your dashboard. Please log in again.</div>
  <?php else: ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h4 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Teacher'); ?></h4>
            <span class="text-muted">
                <?php if($stats['active_school_year']): ?>
                    School Year <?php echo htmlspecialchars($stats['active_school_year']['school_year']); ?>
                <?php else: ?>
                    No active school year set.
                <?php endif; ?>
                <?php if(!empty($stats['my_sections'])): ?>
                    &middot;
                    <?php echo htmlspecialchars(implode(', ', array_map(function($s){ return $s['grade_level_name'] . ' - ' . $s['section_name']; }, $stats['my_sections']))); ?>
                <?php endif; ?>
            </span>
        </div>
    </div>

    <?php if(empty($stats['my_sections'])): ?>
        <div class="alert alert-warning">You don't have an advisory section yet. Contact an administrator to get assigned before adding students.</div>
    <?php endif; ?>

    <!-- Stat cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted d-block mb-1">My Students</span>
                        <h3 class="mb-0"><?php echo (int) $stats['total_students']; ?></h3>
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
                        <span class="text-muted d-block mb-1">Present Today</span>
                        <h3 class="mb-0"><?php echo (int) $stats['attendance_today']['present']; ?> / <?php echo (int) $stats['attendance_today']['total']; ?></h3>
                        <span class="text-muted small"><?php echo (int) $stats['attendance_today']['recorded']; ?> recorded so far</span>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-calendar-check fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted d-block mb-1">Health Profiles</span>
                        <h3 class="mb-0"><?php echo (int) $stats['health_coverage']['covered']; ?> / <?php echo (int) $stats['health_coverage']['total']; ?></h3>
                        <span class="text-muted small">students covered</span>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-band-aid fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted d-block mb-1">Academic Records</span>
                        <h3 class="mb-0"><?php echo (int) $stats['academic_count']; ?></h3>
                        <span class="text-muted small"><?php echo (int) $stats['achievement_count']; ?> achievements</span>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-book fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick links -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Quick Links</h5>
                <div class="list-group list-group-flush">
                    <a href="students.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-user text-primary"></i> Manage Students
                    </a>
                    <a href="attendance.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-calendar-check text-primary"></i> Take Attendance
                    </a>
                    <a href="academic.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-book text-primary"></i> Academic Records
                    </a>
                    <a href="student-behavior.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-note text-primary"></i> Behavior Records
                    </a>
                    <a href="student-developmental.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-note text-primary"></i> Developmental Records
                    </a>
                    <a href="student-health.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-band-aid text-primary"></i> Health Profiles
                    </a>
                    <a href="achievement-profile.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-medal text-primary"></i> Achievements
                    </a>
                    <a href="parent-guardian.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-group text-primary"></i> Parent/Guardian
                    </a>
                    <a href="past-records.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bx bx-history text-primary"></i> Past Records
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent activity -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Recent Activity</h5>
                <div class="list-group list-group-flush">
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
                                    <div><?php echo htmlspecialchars($log['action']); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars(trim(($log['description'] ?? ''), ' ')); ?></div>
                                </div>
                                <span class="text-muted small text-nowrap"><?php echo htmlspecialchars(date('M j, g:i A', strtotime($log['created_at']))); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

  <?php endif; ?>

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
