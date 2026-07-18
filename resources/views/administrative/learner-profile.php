<?php
require_once __DIR__ . '/../../../app/controllers/administrative/learnerprofilecontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/StudentsAge.php';
require_once __DIR__ . '/../../../app/services/LearnerProfileExportService.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['administrative']);

if(isset($_GET['export']) && $_GET['export'] === 'csv' && $profile !== null){
    LearnerProfileExportService::exportCsv($profile);
    exit();
}
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
  <style>
    @media print {
      .no-print { display: none !important; }
      .layout-menu, .layout-navbar, .content-footer { display: none !important; }
      .tab-pane { display: block !important; opacity: 1 !important; }
      .card-header-tabs { display: none !important; }
      .print-section-title { display: block !important; }
    }
    .print-section-title { display: none; }
  </style>
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <p class="text-muted no-print">One consolidated view of everything recorded for a single learner — academic, attendance, behavioral, developmental, health, and achievements — across all sections and teachers.</p>

    <form action="learner-profile.php" method="get" id="learnerSearchForm" class="mb-4 no-print">
        <label for="student_search" class="form-label">Search Learner</label>
        <div class="position-relative" style="max-width: 480px;">
            <input
                type="text"
                class="form-control"
                id="student_search"
                placeholder="Search by name or LRN..."
                autocomplete="off"
                value="<?php echo ($profile !== null) ? htmlspecialchars(LearnerProfileExportService::formatLearnerLabel($profile['info'])) : ''; ?>"
            >
            <div id="student_suggestions" class="list-group shadow-sm position-absolute w-100" style="top: 100%; left: 0; z-index: 5; max-height: 260px; overflow-y: auto; display: none;"></div>
        </div>
        <select class="d-none" id="student_id" name="student_id">
            <option value="">-- Select Learner --</option>
            <?php foreach(($students ?? []) as $s): ?>
                <option value="<?php echo htmlspecialchars($s['id']); ?>" <?php echo ($selected_student_id !== null && (int) $selected_student_id === (int) $s['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars(LearnerProfileExportService::formatLearnerLabel($s)); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if($profile === null): ?>
        <?php if($selected_student_id !== null): ?>
            <div class="alert alert-warning">No learner found for that selection.</div>
        <?php else: ?>
            <div class="alert alert-info">Search for a learner above to view their consolidated profile.</div>
        <?php endif; ?>
    <?php else: ?>
        <?php $info = $profile['info']; ?>
        <?php $fullName = trim($info['first_name'] . ' ' . ($info['middle_name'] ?? '') . ' ' . $info['last_name'] . ' ' . ($info['suffix'] ?? '')); ?>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 no-print">
            <a href="learner-profile.php?status=<?php echo htmlspecialchars($status_filter); ?>&page=<?php echo (int) $page; ?>" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back"></i> Back to List
            </a>
            <div class="d-flex gap-2">
                <a href="?student_id=<?php echo htmlspecialchars($selected_student_id); ?>&status=<?php echo htmlspecialchars($status_filter); ?>&export=csv" class="btn btn-outline-success">
                    <i class="bx bx-file"></i> Export to Excel
                </a>
                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </div>

        <!-- Print-only letterhead -->
        <div class="d-none d-print-flex align-items-center gap-3 mb-4">
            <img src="../../../public/assets/img/favicon/logo.png" alt="School Logo" style="width: 60px; height: 60px;">
            <div>
                <h5 class="mb-0">San Jose Sur Elementary</h5>
                <small class="text-muted">Mallig District &nbsp;•&nbsp; DepEd Region II</small>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <h4 class="mb-0"><?php echo htmlspecialchars($fullName); ?></h4>
                    <span class="badge bg-label-<?php echo ($info['status'] === 'archived') ? 'secondary' : 'success'; ?>">
                        <?php echo htmlspecialchars(ucfirst($info['status'])); ?>
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">LRN</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['lrn'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">Age / Gender</label>
                        <p class="mb-0"><?php echo htmlspecialchars(StudentsAge::calculateAge($info['birth_date'])); ?> &middot; <?php echo htmlspecialchars($info['gender']); ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">Grade & Section</label>
                        <p class="mb-0"><?php echo htmlspecialchars(($info['grade_name'] ?? '') . ' - ' . ($info['section_name'] ?? '')); ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">School Year</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['school_year'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold mb-0">Address</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['address'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold mb-0">Recorded By</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['recorded_by_name'] ?? ''); ?></p>
                    </div>
                </div>

                <?php if(!empty($profile['other_years'])): ?>
                    <hr class="no-print">
                    <label class="form-label fw-bold mb-2 no-print">Also has records for:</label>
                    <div class="d-flex flex-wrap gap-2 no-print">
                        <?php foreach($profile['other_years'] as $other): ?>
                            <a href="learner-profile.php?student_id=<?php echo htmlspecialchars($other['id']); ?>" class="btn btn-sm btn-outline-primary">
                                <?php echo htmlspecialchars($other['school_year'] ?? ''); ?>
                                (<?php echo htmlspecialchars(ucfirst($other['status'])); ?>)
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_academic" type="button">Academic (<?php echo count($profile['academic']); ?>)</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_attendance" type="button">Attendance (<?php echo count($profile['attendance']); ?>)</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_behavioral" type="button">Behavioral (<?php echo count($profile['behavioral']); ?>)</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_developmental" type="button">Developmental (<?php echo count($profile['developmental']); ?>)</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_health" type="button">Health</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_achievements" type="button">Achievements (<?php echo count($profile['achievements']); ?>)</button></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">

                    <div class="tab-pane fade show active" id="tab_academic">
                        <h6 class="print-section-title fw-bold mb-2">Academic Records</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Subject</th><th>Grading Period</th><th>Grade</th><th>Remarks</th><th>School Year</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['academic'])): ?>
                                        <tr><td colspan="6" class="text-center text-muted">No academic records.</td></tr>
                                    <?php else: foreach($profile['academic'] as $r): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['subject_name']); ?></td>
                                            <td><?php echo htmlspecialchars($r['grading_period']); ?></td>
                                            <td><?php echo htmlspecialchars($r['grade']); ?></td>
                                            <td><?php echo htmlspecialchars($r['remarks'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['school_year'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['recorded_by_name'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_attendance">
                        <h6 class="print-section-title fw-bold mb-2">Attendance Records</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Date</th><th>Session</th><th>Status</th><th>Remarks</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['attendance'])): ?>
                                        <tr><td colspan="5" class="text-center text-muted">No attendance records.</td></tr>
                                    <?php else: foreach($profile['attendance'] as $r): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['attendance_date']); ?></td>
                                            <td><?php echo htmlspecialchars($r['session']); ?></td>
                                            <td><?php echo htmlspecialchars($r['status']); ?></td>
                                            <td><?php echo htmlspecialchars($r['remarks'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['recorded_by_name'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_behavioral">
                        <h6 class="print-section-title fw-bold mb-2">Behavioral Records</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Date</th><th>Category</th><th>Observation</th><th>Intervention</th><th>Remarks</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['behavioral'])): ?>
                                        <tr><td colspan="6" class="text-center text-muted">No behavioral records.</td></tr>
                                    <?php else: foreach($profile['behavioral'] as $r): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['observation_date']); ?></td>
                                            <td><?php echo htmlspecialchars($r['category']); ?></td>
                                            <td><?php echo htmlspecialchars($r['observation']); ?></td>
                                            <td><?php echo htmlspecialchars($r['intervention'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['remarks'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['recorded_by_name'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_developmental">
                        <h6 class="print-section-title fw-bold mb-2">Developmental Records</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Domain</th><th>Observation</th><th>Recommendation</th><th>School Year</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['developmental'])): ?>
                                        <tr><td colspan="5" class="text-center text-muted">No developmental records.</td></tr>
                                    <?php else: foreach($profile['developmental'] as $r): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['domain']); ?></td>
                                            <td><?php echo htmlspecialchars($r['observation']); ?></td>
                                            <td><?php echo htmlspecialchars($r['recommendation'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['school_year'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['recorded_by_name'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_health">
                        <h6 class="print-section-title fw-bold mb-2">Health Profile</h6>
                        <?php $h = $profile['health']; ?>
                        <?php if(!$h): ?>
                            <p class="text-muted">No health profile recorded.</p>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Height / Weight</label><p class="mb-0"><?php echo htmlspecialchars($h['height_cm'] ?? ''); ?> cm / <?php echo htmlspecialchars($h['weight_kg'] ?? ''); ?> kg</p></div>
                                <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">BMI</label><p class="mb-0"><?php echo htmlspecialchars($h['bmi'] ?? ''); ?> (<?php echo htmlspecialchars($h['bmi_classification'] ?? ''); ?>)</p></div>
                                <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Blood Type</label><p class="mb-0"><?php echo htmlspecialchars($h['blood_type'] ?? ''); ?></p></div>
                                <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">School Year</label><p class="mb-0"><?php echo htmlspecialchars($h['school_year'] ?? ''); ?></p></div>
                                <div class="col-md-6 mb-3"><label class="form-label fw-bold mb-0">Allergies</label><p class="mb-0"><?php echo htmlspecialchars($h['allergies'] ?? ''); ?></p></div>
                                <div class="col-md-6 mb-3"><label class="form-label fw-bold mb-0">Medical Conditions</label><p class="mb-0"><?php echo htmlspecialchars($h['medical_conditions'] ?? ''); ?></p></div>
                                <div class="col-md-6 mb-3"><label class="form-label fw-bold mb-0">Vision Screening</label><p class="mb-0"><?php echo htmlspecialchars($h['vision_screening_result'] ?? ''); ?></p></div>
                                <div class="col-md-6 mb-3"><label class="form-label fw-bold mb-0">Hearing Screening</label><p class="mb-0"><?php echo htmlspecialchars($h['hearing_screening_result'] ?? ''); ?></p></div>
                                <div class="col-12 mb-3"><label class="form-label fw-bold mb-0">Immunization Status</label><p class="mb-0"><?php echo htmlspecialchars($h['immunization_status'] ?? ''); ?></p></div>
                                <div class="col-12"><label class="form-label fw-bold mb-0">Recorded By</label><p class="mb-0"><?php echo htmlspecialchars($h['recorded_by_name'] ?? ''); ?></p></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="tab_achievements">
                        <h6 class="print-section-title fw-bold mb-2">Achievement Records</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Title</th><th>Category</th><th>Level</th><th>Date Received</th><th>Awarding Body</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['achievements'])): ?>
                                        <tr><td colspan="6" class="text-center text-muted">No achievement records.</td></tr>
                                    <?php else: foreach($profile['achievements'] as $r): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['title']); ?></td>
                                            <td><?php echo htmlspecialchars($r['category']); ?></td>
                                            <td><?php echo htmlspecialchars($r['level']); ?></td>
                                            <td><?php echo htmlspecialchars($r['date_received']); ?></td>
                                            <td><?php echo htmlspecialchars($r['awarding_body'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['recorded_by_name'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if($profile === null): ?>
    <div class="card mt-4 no-print">
        <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>All Learners</span>
            <div class="btn-group" role="group">
                <a href="?status=active" class="btn btn-sm <?php echo ($status_filter === 'active') ? 'btn-primary' : 'btn-outline-primary'; ?>">Active</a>
                <a href="?status=archived" class="btn btn-sm <?php echo ($status_filter === 'archived') ? 'btn-primary' : 'btn-outline-primary'; ?>">Archived</a>
            </div>
        </h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>LRN</th>
                        <th>Name</th>
                        <th>Grade & Section</th>
                        <th>School Year</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $masterRows   = $masterList['data']         ?? [];
                        $masterPage   = $masterList['current_page'] ?? 1;
                        $masterPages  = $masterList['total_pages']  ?? 1;
                        $masterPer    = $masterList['per_page']     ?? 10;
                        $masterOffset = ($masterPage - 1) * $masterPer;
                    ?>
                    <?php if(empty($masterRows)): ?>
                        <tr><td colspan="6" class="text-center text-muted">No learners found.</td></tr>
                    <?php else: ?>
                        <?php foreach($masterRows as $index => $s): ?>
                            <?php $rowName = trim($s['last_name'] . ', ' . $s['first_name'] . ' ' . ($s['middle_name'] ?? '') . ' ' . ($s['suffix'] ?? '')); ?>
                            <tr
                                role="button"
                                style="cursor: pointer;"
                                class="<?php echo ($selected_student_id !== null && (int) $selected_student_id === (int) $s['id']) ? 'table-active' : ''; ?>"
                                onclick="window.location.href='learner-profile.php?student_id=<?php echo htmlspecialchars($s['id']); ?>&page=<?php echo $masterPage; ?>&status=<?php echo htmlspecialchars($status_filter); ?>'"
                            >
                                <td><?php echo $masterOffset + $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($s['lrn'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($rowName); ?></td>
                                <td><?php echo htmlspecialchars(($s['grade_name'] ?? '') . ' - ' . ($s['section_name'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($s['school_year'] ?? ''); ?></td>
                                <td>
                                    <span class="badge bg-label-<?php echo ($s['status'] === 'archived') ? 'secondary' : 'success'; ?>">
                                        <?php echo htmlspecialchars(ucfirst($s['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($masterPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $masterPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?status=<?php echo htmlspecialchars($status_filter); ?>&page=<?php echo $masterPage - 1; ?>">&laquo;</a>
              </li>
              <?php for ($p = 1; $p <= $masterPages; $p++): ?>
                <li class="page-item <?php echo $p === $masterPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?status=<?php echo htmlspecialchars($status_filter); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $masterPage >= $masterPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?status=<?php echo htmlspecialchars($status_filter); ?>&page=<?php echo $masterPage + 1; ?>">&raquo;</a>
              </li>
            </ul>
          </nav>
        </div>
        <?php endif; ?>
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
  <script src="../../../public/js/administrative/learner-profile.js"></script>
</body>
</html>
