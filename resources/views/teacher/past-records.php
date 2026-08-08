<?php
require_once __DIR__ . '/../../../app/controllers/teacher/PastRecordsController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Past Records</title>
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

    <p class="text-muted">Students who were rolled over to a new school year no longer appear in your active lists — this page keeps their records available for reference.</p>

    <?php if($profile === null): ?>

        <form action="past-records.php" method="get" class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <div>
                <label for="school_year_id" class="form-label mb-0">School Year</label>
                <select class="form-select" id="school_year_id" name="school_year_id" onchange="this.form.submit()">
                    <option value="">-- All School Years --</option>
                    <?php foreach(($school_years ?? []) as $school_year): ?>
                        <option value="<?php echo htmlspecialchars($school_year['id']); ?>" <?php echo ($school_year_filter !== null && (int) $school_year_filter === (int) $school_year['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($school_year['school_year']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <div class="card mt-4">
            <h5 class="card-header">Past Students</h5>
            <div class="table-responsive nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>LRN</th>
                            <th>Name</th>
                            <th>Grade & Section</th>
                            <th>School Year</th>
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
                            <tr><td colspan="5" class="text-center text-muted">No past records found for the selected filters.</td></tr>
                        <?php else: ?>
                            <?php foreach($masterRows as $index => $s): ?>
                                <?php $rowName = trim($s['last_name'] . ', ' . $s['first_name'] . ' ' . ($s['middle_name'] ?? '') . ' ' . ($s['suffix'] ?? '')); ?>
                                <tr
                                    role="button"
                                    style="cursor: pointer;"
                                    onclick="window.location.href='past-records.php?student_id=<?php echo htmlspecialchars($s['id']); ?>&school_year_id=<?php echo htmlspecialchars($school_year_filter ?? ''); ?>&page=<?php echo $masterPage; ?>'"
                                >
                                    <td><?php echo $masterOffset + $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($s['lrn'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($rowName); ?></td>
                                    <td><?php echo htmlspecialchars(($s['grade_name'] ?? '') . ' - ' . ($s['section_name'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($s['school_year'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php $masterQuery = $school_year_filter !== null ? 'school_year_id=' . $school_year_filter . '&' : ''; ?>
            <?php if ($masterPages > 1): ?>
            <div class="card-footer">
              <nav>
                <ul class="pagination justify-content-center mb-0">
                  <li class="page-item <?php echo $masterPage <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo $masterQuery; ?>page=<?php echo $masterPage - 1; ?>">&laquo;</a>
                  </li>
                  <?php for ($p = 1; $p <= $masterPages; $p++): ?>
                    <li class="page-item <?php echo $p === $masterPage ? 'active' : ''; ?>">
                      <a class="page-link" href="?<?php echo $masterQuery; ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
                    </li>
                  <?php endfor; ?>
                  <li class="page-item <?php echo $masterPage >= $masterPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo $masterQuery; ?>page=<?php echo $masterPage + 1; ?>">&raquo;</a>
                  </li>
                </ul>
              </nav>
            </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <?php $info = $profile['info']; ?>
        <?php $fullName = trim($info['first_name'] . ' ' . ($info['middle_name'] ?? '') . ' ' . $info['last_name'] . ' ' . ($info['suffix'] ?? '')); ?>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <a href="past-records.php?school_year_id=<?php echo htmlspecialchars($school_year_filter ?? ''); ?>&page=<?php echo (int) $page; ?>" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back"></i> Back to List
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <h4 class="mb-0"><?php echo htmlspecialchars($fullName); ?></h4>
                    <span class="badge bg-label-secondary">Archived</span>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">LRN</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['lrn'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">Grade & Section</label>
                        <p class="mb-0"><?php echo htmlspecialchars(($info['grade_name'] ?? '') . ' - ' . ($info['section_name'] ?? '')); ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">School Year</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['school_year'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">Recorded By</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['recorded_by_name'] ?? ''); ?></p>
                    </div>
                </div>

                <hr>
                <h6 class="fw-bold mb-3">Parent/Guardian Information</h6>
                <?php $pg = $profile['parent_guardian']; ?>
                <?php if(!$pg): ?>
                    <p class="text-muted">No parent/guardian record found for this student.</p>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Father's Name</label><p class="mb-0"><?php echo htmlspecialchars($pg['father_name'] ?? ''); ?></p></div>
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Father's Occupation</label><p class="mb-0"><?php echo htmlspecialchars($pg['father_occupation'] ?? ''); ?></p></div>
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Father's Contact</label><p class="mb-0"><?php echo htmlspecialchars($pg['father_contact'] ?? ''); ?></p></div>
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Mother's Name</label><p class="mb-0"><?php echo htmlspecialchars($pg['mother_name'] ?? ''); ?></p></div>
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Mother's Occupation</label><p class="mb-0"><?php echo htmlspecialchars($pg['mother_occupation'] ?? ''); ?></p></div>
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Mother's Contact</label><p class="mb-0"><?php echo htmlspecialchars($pg['mother_contact'] ?? ''); ?></p></div>
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Guardian's Name</label><p class="mb-0"><?php echo htmlspecialchars($pg['guardian_name'] ?? ''); ?></p></div>
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Guardian Relationship</label><p class="mb-0"><?php echo htmlspecialchars($pg['guardian_relationship'] ?? ''); ?></p></div>
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold mb-0">Guardian's Contact</label><p class="mb-0"><?php echo htmlspecialchars($pg['guardian_contact'] ?? ''); ?></p></div>
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
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_reading_level" type="button">Reading Level (<?php echo count($profile['reading_level']); ?>)</button></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">

                    <div class="tab-pane fade show active" id="tab_academic">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Subject</th><th>Grading Period</th><th>Grade</th><th>Remarks</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['academic'])): ?>
                                        <tr><td colspan="5" class="text-center text-muted">No past records found for the selected filters.</td></tr>
                                    <?php else: foreach($profile['academic'] as $r): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['subject_name']); ?></td>
                                            <td><?php echo htmlspecialchars($r['grading_period']); ?></td>
                                            <td><?php echo htmlspecialchars($r['grade']); ?></td>
                                            <td><?php echo htmlspecialchars($r['remarks'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['recorded_by_name'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_attendance">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Date</th><th>Session</th><th>Status</th><th>Remarks</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['attendance'])): ?>
                                        <tr><td colspan="5" class="text-center text-muted">No past records found for the selected filters.</td></tr>
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
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Date</th><th>Category</th><th>Observation</th><th>Intervention</th><th>Remarks</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['behavioral'])): ?>
                                        <tr><td colspan="6" class="text-center text-muted">No past records found for the selected filters.</td></tr>
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
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Domain</th><th>Observation</th><th>Recommendation</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['developmental'])): ?>
                                        <tr><td colspan="4" class="text-center text-muted">No past records found for the selected filters.</td></tr>
                                    <?php else: foreach($profile['developmental'] as $r): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['domain']); ?></td>
                                            <td><?php echo htmlspecialchars($r['observation']); ?></td>
                                            <td><?php echo htmlspecialchars($r['recommendation'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($r['recorded_by_name'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_health">
                        <?php $h = $profile['health']; ?>
                        <?php if(!$h): ?>
                            <p class="text-muted">No past records found for the selected filters.</p>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Height / Weight</label><p class="mb-0"><?php echo htmlspecialchars($h['height_cm'] ?? ''); ?> cm / <?php echo htmlspecialchars($h['weight_kg'] ?? ''); ?> kg</p></div>
                                <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">BMI</label><p class="mb-0"><?php echo htmlspecialchars($h['bmi'] ?? ''); ?> (<?php echo htmlspecialchars($h['bmi_classification'] ?? ''); ?>)</p></div>
                                <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Blood Type</label><p class="mb-0"><?php echo htmlspecialchars($h['blood_type'] ?? ''); ?></p></div>
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
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Title</th><th>Category</th><th>Level</th><th>Date Received</th><th>Awarding Body</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['achievements'])): ?>
                                        <tr><td colspan="6" class="text-center text-muted">No past records found for the selected filters.</td></tr>
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

                    <div class="tab-pane fade" id="tab_reading_level">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Assessment Date</th><th>Reading Level</th><th>Reading Language</th><th>Remarks</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['reading_level'])): ?>
                                        <tr><td colspan="5" class="text-center text-muted">No past records found for the selected filters.</td></tr>
                                    <?php else: foreach($profile['reading_level'] as $r): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['assessment_date']); ?></td>
                                            <td><?php echo htmlspecialchars($r['reading_level']); ?></td>
                                            <td><?php echo htmlspecialchars($r['reading_language']); ?></td>
                                            <td><?php echo htmlspecialchars($r['remarks'] ?? ''); ?></td>
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

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../../public/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../../../public/assets/vendor/libs/popper/popper.js"></script>
  <script src="../../../public/assets/vendor/js/bootstrap.js"></script>
  <script src="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../../../public/assets/vendor/js/menu.js"></script>
  <script src="../../../public/assets/js/main.js"></script>
</body>
</html>
