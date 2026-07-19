<?php
session_start();
require_once __DIR__ . '/../../../app/controllers/teacher/AttendanceController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

$controller = new AttendanceController($con);
$attendance_records = $controller->history();
$students = $controller->getStudents();
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
  <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Attendance</title>
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
  <style>
    .attendance-status-btn {
      min-width: 84px;
    }
    .attendance-status-btn.status-present.active { background-color: #28a745; border-color: #28a745; color: #fff; }
    .attendance-status-btn.status-absent.active  { background-color: #dc3545; border-color: #dc3545; color: #fff; }
    .attendance-status-btn.status-late.active    { background-color: #ffc107; border-color: #ffc107; color: #000; }
    .attendance-status-btn.status-excused.active { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }
    #attendanceGridTable thead th {
      position: sticky;
      top: 0;
      background: #fff;
      z-index: 1;
    }
    .status-badge-Present { color: #28a745; font-weight: 600; }
    .status-badge-Absent  { color: #dc3545; font-weight: 600; }
    .status-badge-Late    { color: #d39e00; font-weight: 600; }
    .status-badge-Excused { color: #0d6efd; font-weight: 600; }
    .status-badge-empty   { color: #adb5bd; font-weight: 400; }
    .session-group {
      display: inline-flex;
      gap: 4px;
      padding: 4px 8px;
      border-radius: 4px;
      background: rgba(0,0,0,0.02);
    }
    .session-divider {
      border-left: 2px solid #dee2e6;
    }
    .attendance-locked-cell {
      background-color: rgba(0, 0, 0, 0.035);
    }
    .attendance-status-btn.locked {
      cursor: not-allowed;
      opacity: 0.85;
    }
  </style>
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <ul class="nav nav-tabs mb-3" id="attendanceTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="history-tab-btn" data-bs-toggle="tab" data-bs-target="#historyTab" type="button" role="tab">Attendance History</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="take-tab-btn" data-bs-toggle="tab" data-bs-target="#takeTab" type="button" role="tab">Take Attendance</button>
      </li>
    </ul>

    <div class="tab-content">

      <!-- TAB 1: Attendance History -->
      <div class="tab-pane fade show active" id="historyTab" role="tabpanel">
        <div class="card">
          <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>Attendance History</span>
            <div class="d-flex gap-2 flex-wrap">
              <select class="form-select form-select-sm" id="historyStudentFilter" style="width: auto;">
                <option value="">All students</option>
                <?php foreach (($students ?? []) as $student): ?>
                  <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']); ?>
                  <option value="<?= htmlspecialchars($studentName) ?>"><?= htmlspecialchars($studentName) ?></option>
                <?php endforeach; ?>
              </select>
              <select class="form-select form-select-sm" id="historySessionFilter" style="width: auto;">
                <option value="">All sessions</option>
                <option value="Morning">Morning</option>
                <option value="Afternoon">Afternoon</option>
              </select>
              <select class="form-select form-select-sm" id="historyStatusFilter" style="width: auto;">
                <option value="">All statuses</option>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
                <option value="Excused">Excused</option>
              </select>
              <input type="date" class="form-control form-control-sm" id="historyDateFilter" style="width: auto;">
              <button type="button" class="btn btn-sm btn-outline-secondary" id="historyFilterClear">Clear</button>
            </div>
          </h5>
          <div class="table-responsive nowrap">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Student Name</th>
                  <th>Date</th>
                  <th>Morning</th>
                  <th>Afternoon</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody id="historyTableBody">
                <?php if (!empty($attendance_records)): ?>
                  <?php foreach ($attendance_records as $index => $record): ?>
                    <?php
                      $recordStudentName = trim($record['student_first_name'] . ' ' . ($record['student_middle_name'] ?? '') . ' ' . $record['student_last_name'] . ' ' . ($record['student_suffix'] ?? ''));
                      $morningStatus = $record['morning_status'] ?? null;
                      $afternoonStatus = $record['afternoon_status'] ?? null;
                      $morningRemarks = trim($record['morning_remarks'] ?? '');
                      $afternoonRemarks = trim($record['afternoon_remarks'] ?? '');
                      if ($morningRemarks !== '' && $afternoonRemarks !== '') {
                          $remarksDisplay = 'AM: ' . $morningRemarks . ' / PM: ' . $afternoonRemarks;
                      } else {
                          $remarksDisplay = $morningRemarks !== '' ? $morningRemarks : $afternoonRemarks;
                      }
                    ?>
                    <tr
                      class="history-row"
                      data-student="<?= htmlspecialchars($recordStudentName) ?>"
                      data-date="<?= htmlspecialchars($record['attendance_date']) ?>"
                      data-morning-status="<?= htmlspecialchars($morningStatus ?? '') ?>"
                      data-afternoon-status="<?= htmlspecialchars($afternoonStatus ?? '') ?>"
                    >
                      <td><?= $index + 1 ?></td>
                      <td><?= htmlspecialchars($recordStudentName) ?></td>
                      <td><?= htmlspecialchars($record['attendance_date']) ?></td>
                      <td>
                        <?php if ($morningStatus): ?>
                          <span class="status-badge-<?= htmlspecialchars($morningStatus) ?>"><?= htmlspecialchars($morningStatus) ?></span>
                        <?php else: ?>
                          <span class="status-badge-empty">&mdash;</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($afternoonStatus): ?>
                          <span class="status-badge-<?= htmlspecialchars($afternoonStatus) ?>"><?= htmlspecialchars($afternoonStatus) ?></span>
                        <?php else: ?>
                          <span class="status-badge-empty">&mdash;</span>
                        <?php endif; ?>
                      </td>
                      <td><?= htmlspecialchars($remarksDisplay) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center">No attendance records found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small" id="historyPaginationInfo"></span>
            <nav>
              <ul class="pagination pagination-sm mb-0" id="historyPagination"></ul>
            </nav>
          </div>
        </div>
      </div>

      <!-- TAB 2: Take Attendance -->
      <div class="tab-pane fade" id="takeTab" role="tabpanel">
        <div class="card">
          <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>Take Attendance</span>
            <div class="d-flex align-items-center gap-2">
              <label for="attendanceGridDate" class="form-label mb-0">Date</label>
              <input type="date" class="form-control form-control-sm" id="attendanceGridDate" style="width: auto;">
            </div>
          </h5>
          <div id="attendanceGridAlert" class="alert alert-warning m-3 d-none"></div>
          <div class="table-responsive nowrap" style="max-height: 65vh;">
            <table class="table table-bordered mb-0" id="attendanceGridTable">
              <thead>
                <tr>
                  <th rowspan="2" class="align-middle">Student Name</th>
                  <th colspan="4" class="text-center">Morning</th>
                  <th colspan="4" class="text-center session-divider">Afternoon</th>
                </tr>
                <tr>
                  <th class="text-center">Present</th>
                  <th class="text-center">Absent</th>
                  <th class="text-center">Late</th>
                  <th class="text-center">Excused</th>
                  <th class="text-center session-divider">Present</th>
                  <th class="text-center">Absent</th>
                  <th class="text-center">Late</th>
                  <th class="text-center">Excused</th>
                </tr>
              </thead>
              <tbody id="attendanceGridBody">
                <tr><td colspan="9" class="text-center text-muted">Loading students...</td></tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer text-end">
            <button type="button" class="btn btn-primary" id="saveAttendanceBtn">Save Attendance</button>
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
  <script src="../../../public/js/teacher/attendance.js"></script>
</body>
</html>
