<?php
require_once __DIR__ . '/../../../app/controllers/teacher/StudentsController.php';
require_once __DIR__ . '/../../../app/models/administrative/learnerprofilemodel.php';
require_once __DIR__ . '/../../../app/helpers/StudentsAge.php';
require_once __DIR__ . '/../../../app/helpers/SchoolSettings.php';
require_once __DIR__ . '/../../../app/services/AddressService.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

$school = SchoolSettings::get($con);
$teacherId = (int) ($_SESSION['id'] ?? 0);
$studentId = isset($_GET['id']) && $_GET['id'] !== '' ? (int) $_GET['id'] : 0;

$studentsModel = new StudentsModel($con);
if($studentId <= 0 || !$studentsModel->belongsToAdviser($studentId, $teacherId)){
    http_response_code(403);
    die('You do not have access to print this student record.');
}

$profileModel = new LearnerProfileModel($con);
$info = $profileModel->getStudentInfo($studentId);
if(!$info){
    http_response_code(404);
    die('Student record not found.');
}

$academic = $profileModel->getAcademicRecords($studentId);
$health = $profileModel->getHealthProfile($studentId);
$reading_level = $profileModel->getReadingLevelRecords($studentId);
$parent_guardian = $profileModel->getParentGuardian($studentId);

$fullName = trim($info['first_name'] . ' ' . ($info['middle_name'] ?? '') . ' ' . $info['last_name'] . ' ' . ($info['suffix'] ?? ''));
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Student Profile</title>
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

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 no-print">
        <a href="students.php" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Back to Manage Students
        </a>
        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
            <i class="bx bx-printer"></i> Print
        </button>
    </div>

    <!-- Print-only letterhead -->
    <div class="d-none d-print-flex align-items-center gap-3 mb-4">
        <img src="../../../public/assets/img/favicon/logo.png" alt="School Logo" style="width: 60px; height: 60px;">
        <div>
            <h5 class="mb-0"><?php echo htmlspecialchars($school['school_name']); ?></h5>
            <small class="text-muted"><?php echo htmlspecialchars($school['district']); ?> &nbsp;&bull;&nbsp; DepEd <?php echo htmlspecialchars($school['region']); ?></small>
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
                    <label class="form-label fw-bold mb-0">Age / Sex</label>
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
                    <p class="mb-0"><?php echo htmlspecialchars(AddressService::formatFullAddress($info)); ?></p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold mb-0">Recorded By</label>
                    <p class="mb-0"><?php echo htmlspecialchars($info['recorded_by_name'] ?? ''); ?></p>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label fw-bold mb-0">Age (as of June)</label>
                    <p class="mb-0"><?php echo htmlspecialchars($info['age_as_of_june'] ?? ''); ?></p>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label fw-bold mb-0">Mother Tongue</label>
                    <p class="mb-0"><?php echo htmlspecialchars($info['mother_tongue'] ?? ''); ?></p>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label fw-bold mb-0">IP / Ethnic Group</label>
                    <p class="mb-0"><?php echo htmlspecialchars($info['ip_ethnic_group'] ?? ''); ?></p>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label fw-bold mb-0">Religion</label>
                    <p class="mb-0"><?php echo htmlspecialchars($info['religion'] ?? ''); ?></p>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label fw-bold mb-0">Learning Modality</label>
                    <p class="mb-0"><?php echo htmlspecialchars($info['learning_modality'] ?? ''); ?></p>
                </div>
                <div class="col-md-9 mb-3">
                    <label class="form-label fw-bold mb-0">Remarks</label>
                    <p class="mb-0"><?php echo htmlspecialchars($info['remarks'] ?? ''); ?></p>
                </div>
            </div>

            <hr>
            <h6 class="fw-bold mb-3">Parent/Guardian Information</h6>
            <?php if(!$parent_guardian): ?>
                <p class="text-muted">No parent/guardian record found for this learner.</p>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Father's Name</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['father_name'] ?? ''); ?></p></div>
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Father's Occupation</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['father_occupation'] ?? ''); ?></p></div>
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Father's Contact</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['father_contact'] ?? ''); ?></p></div>
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Mother's Name</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['mother_name'] ?? ''); ?></p></div>
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Mother's Occupation</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['mother_occupation'] ?? ''); ?></p></div>
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Mother's Contact</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['mother_contact'] ?? ''); ?></p></div>
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Guardian's Name</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['guardian_name'] ?? ''); ?></p></div>
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Guardian Relationship</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['guardian_relationship'] ?? ''); ?></p></div>
                    <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Guardian's Contact</label><p class="mb-0"><?php echo htmlspecialchars($parent_guardian['guardian_contact'] ?? ''); ?></p></div>
                </div>
            <?php endif; ?>

            <hr>
            <h6 class="fw-bold mb-3">Height / Weight</h6>
            <?php if(!$health): ?>
                <p class="text-muted">No health profile recorded.</p>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Height</label><p class="mb-0"><?php echo htmlspecialchars($health['height_cm'] ?? ''); ?> cm</p></div>
                    <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Weight</label><p class="mb-0"><?php echo htmlspecialchars($health['weight_kg'] ?? ''); ?> kg</p></div>
                    <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">BMI</label><p class="mb-0"><?php echo htmlspecialchars($health['bmi'] ?? ''); ?></p></div>
                    <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Classification</label><p class="mb-0"><?php echo htmlspecialchars($health['bmi_classification'] ?? ''); ?></p></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_academic" type="button">Academic (<?php echo count($academic); ?>)</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_health" type="button">Health</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_reading_level" type="button">Reading Level (<?php echo count($reading_level); ?>)</button></li>
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
                                <?php if(empty($academic)): ?>
                                    <tr><td colspan="6" class="text-center text-muted">No academic records.</td></tr>
                                <?php else: foreach($academic as $r): ?>
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

                <div class="tab-pane fade" id="tab_health">
                    <h6 class="print-section-title fw-bold mb-2">Health Profile</h6>
                    <?php if(!$health): ?>
                        <p class="text-muted">No health profile recorded.</p>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Height / Weight</label><p class="mb-0"><?php echo htmlspecialchars($health['height_cm'] ?? ''); ?> cm / <?php echo htmlspecialchars($health['weight_kg'] ?? ''); ?> kg</p></div>
                            <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">BMI</label><p class="mb-0"><?php echo htmlspecialchars($health['bmi'] ?? ''); ?> (<?php echo htmlspecialchars($health['bmi_classification'] ?? ''); ?>)</p></div>
                            <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Blood Type</label><p class="mb-0"><?php echo htmlspecialchars($health['blood_type'] ?? ''); ?></p></div>
                            <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">School Year</label><p class="mb-0"><?php echo htmlspecialchars($health['school_year'] ?? ''); ?></p></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold mb-0">Allergies</label><p class="mb-0"><?php echo htmlspecialchars($health['allergies'] ?? ''); ?></p></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold mb-0">Medical Conditions</label><p class="mb-0"><?php echo htmlspecialchars($health['medical_conditions'] ?? ''); ?></p></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold mb-0">Vision Screening</label><p class="mb-0"><?php echo htmlspecialchars($health['vision_screening_result'] ?? ''); ?></p></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold mb-0">Hearing Screening</label><p class="mb-0"><?php echo htmlspecialchars($health['hearing_screening_result'] ?? ''); ?></p></div>
                            <div class="col-12 mb-3"><label class="form-label fw-bold mb-0">Immunization Status</label><p class="mb-0"><?php echo htmlspecialchars($health['immunization_status'] ?? ''); ?></p></div>
                            <div class="col-12"><label class="form-label fw-bold mb-0">Recorded By</label><p class="mb-0"><?php echo htmlspecialchars($health['recorded_by_name'] ?? ''); ?></p></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="tab_reading_level">
                    <h6 class="print-section-title fw-bold mb-2">Reading Level Records</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Assessment Date</th><th>Reading Level</th><th>Reading Language</th><th>Remarks</th><th>School Year</th><th>Recorded By</th></tr></thead>
                            <tbody>
                                <?php if(empty($reading_level)): ?>
                                    <tr><td colspan="6" class="text-center text-muted">No reading level records.</td></tr>
                                <?php else: foreach($reading_level as $r): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($r['assessment_date']); ?></td>
                                        <td><?php echo htmlspecialchars($r['reading_level']); ?></td>
                                        <td><?php echo htmlspecialchars($r['reading_language']); ?></td>
                                        <td><?php echo htmlspecialchars($r['remarks'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($r['school_year'] ?? ''); ?></td>
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

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

  <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
  <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
  <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../../../public/assets/vendor/js/menu.js"></script>
  <script src="../../../public/assets/js/main.js"></script>
</body>
</html>
