<?php
require_once __DIR__ . '/../../../app/controllers/administrative/learnerprofilecontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/StudentsAge.php';
require_once __DIR__ . '/../../../app/helpers/SchoolSettings.php';
require_once __DIR__ . '/../../../app/services/LearnerProfileExportService.php';
require_once __DIR__ . '/../../../app/services/AddressService.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['administrative']);

$school = SchoolSettings::get($con);
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
  <link rel="stylesheet" href="../../../public/assets/css/profile-settings.css" />
  <link rel="stylesheet" href="../../../public/assets/css/learner-profile.css" />
  <link rel="stylesheet" href="../../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <script src="../../../public/assets/vendor/js/helpers.js"></script>
  <script src="../../../public/assets/js/config.js"></script>
  <style>
    .learner-avatar-img { cursor: zoom-in; }
    .photo-zoom-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      z-index: 2000;
      align-items: center;
      justify-content: center;
      cursor: zoom-out;
    }
    .photo-zoom-overlay.show { display: flex; }
    .photo-zoom-overlay img { max-width: 90vw; max-height: 90vh; border-radius: .5rem; box-shadow: 0 0 24px rgba(0,0,0,.5); }
    .photo-zoom-overlay-close { position: absolute; top: 20px; right: 28px; color: #fff; font-size: 2rem; line-height: 1; cursor: pointer; }
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

    <div id="photoZoomOverlay" class="photo-zoom-overlay no-print" onclick="closePhotoZoom()">
        <span class="photo-zoom-overlay-close" onclick="closePhotoZoom()">&times;</span>
        <img id="photoZoomOverlayImg" src="" alt="Learner's photo (enlarged)">
    </div>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <p class="text-muted no-print">One consolidated view of everything recorded for a single learner — academic, health, and reading level — across all sections and teachers.</p>

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
        <?php
            $learnerDefaultAvatar = '../../../public/assets/img/avatars/1.png';
            $learnerAvatarSrc = $info['picture'] ?? null;
            if($learnerAvatarSrc){
                $learnerAvatarRelPath = (strpos($learnerAvatarSrc, '/') !== false) ? $learnerAvatarSrc : ('storage/students/' . $learnerAvatarSrc);
                $learnerAvatarFullPath = __DIR__ . '/../../../' . $learnerAvatarRelPath;
                $learnerAvatarSrc = file_exists($learnerAvatarFullPath) ? ('../../../' . $learnerAvatarRelPath) : $learnerDefaultAvatar;
            }else{
                $learnerAvatarSrc = $learnerDefaultAvatar;
            }
        ?>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 no-print">
            <a href="learner-profile.php?status=<?php echo htmlspecialchars($status_filter); ?>&page=<?php echo (int) $page; ?>" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back"></i> Back to List
            </a>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </div>

        <!-- Print-only letterhead -->
        <div class="d-none d-print-flex align-items-center gap-3 mb-4">
            <img src="../../../public/assets/img/favicon/logo.png" alt="School Logo" style="width: 60px; height: 60px;">
            <div>
                <h5 class="mb-0"><?php echo htmlspecialchars($school['school_name']); ?></h5>
                <small class="text-muted"><?php echo htmlspecialchars($school['district']); ?> &nbsp;•&nbsp; DepEd <?php echo htmlspecialchars($school['region']); ?></small>
            </div>
        </div>

        <div class="card learner-header-card mb-4">
            <div class="card-body">
                <div class="learner-header-body">
                    <img src="<?php echo htmlspecialchars($learnerAvatarSrc); ?>" alt="Learner's photo" class="learner-avatar-img" onclick="openPhotoZoom(this.src)">
                    <div class="learner-header-info">
                        <h5 class="mb-0"><?php echo htmlspecialchars($fullName); ?></h5>
                        <p class="text-muted mb-2"><?php echo htmlspecialchars(($info['grade_name'] ?? '') . ' - ' . ($info['section_name'] ?? '')); ?> &middot; LRN <?php echo htmlspecialchars($info['lrn'] ?? 'N/A'); ?></p>
                        <span class="badge bg-label-<?php echo ($info['status'] === 'archived') ? 'secondary' : 'success'; ?>">
                            <?php echo htmlspecialchars(ucfirst($info['status'])); ?>
                        </span>
                        <span class="text-muted small ms-2"><i class='bx bx-calendar'></i> <?php echo htmlspecialchars($info['school_year'] ?? ''); ?></span>
                    </div>
                </div>

                <hr>
                <p class="settings-section-title">Learner Information</p>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0"><i class='bx bx-calendar'></i> Birthdate</label>
                        <p class="mb-0"><?php echo !empty($info['birth_date']) ? htmlspecialchars((new DateTime($info['birth_date']))->format('F j, Y')) : ''; ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">Age / Sex</label>
                        <p class="mb-0"><?php echo htmlspecialchars(StudentsAge::calculateAge($info['birth_date'])); ?> &middot; <?php echo htmlspecialchars($info['gender']); ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">Age (as of June)</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['age_as_of_june'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label fw-bold mb-0">Recorded By</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['recorded_by_name'] ?? ''); ?></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold mb-0"><i class='bx bx-map-pin'></i> Address</label>
                        <p class="mb-0"><?php echo htmlspecialchars(AddressService::formatFullAddress($info)); ?></p>
                    </div>
                </div>

                <hr>
                <p class="settings-section-title">Background Information</p>
                <div class="row">
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
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold mb-0">Remarks</label>
                        <p class="mb-0"><?php echo htmlspecialchars($info['remarks'] ?? ''); ?></p>
                    </div>
                </div>

                <hr>
                <p class="settings-section-title">Parent/Guardian Information</p>
                <?php $pg = $profile['parent_guardian']; ?>
                <?php if(!$pg): ?>
                    <p class="text-muted">No parent/guardian record found for this learner.</p>
                <?php else: ?>
                    <?php
                        $pgPersons = [
                            ['label' => 'Father', 'prefix' => 'father_', 'nameKey' => 'father_name', 'ageKey' => 'fathers_age', 'roleLabel' => 'Occupation', 'roleKey' => 'father_occupation', 'contactKey' => 'father_contact'],
                            ['label' => 'Mother', 'prefix' => 'mother_', 'nameKey' => 'mother_name', 'ageKey' => 'mothers_age', 'roleLabel' => 'Occupation', 'roleKey' => 'mother_occupation', 'contactKey' => 'mother_contact'],
                            ['label' => 'Guardian', 'prefix' => 'guardian_', 'nameKey' => 'guardian_name', 'ageKey' => 'guardians_age', 'roleLabel' => 'Relationship', 'roleKey' => 'guardian_relationship', 'contactKey' => 'guardian_contact'],
                        ];
                    ?>
                    <div class="row">
                        <?php foreach($pgPersons as $person): ?>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <p class="fw-bold mb-2"><i class='bx bx-user'></i> <?php echo htmlspecialchars($person['label']); ?></p>
                                    <label class="form-label small text-muted mb-0">Name</label>
                                    <p class="mb-2"><?php echo htmlspecialchars($pg[$person['nameKey']] ?? '') ?: '—'; ?></p>
                                    <label class="form-label small text-muted mb-0">Age</label>
                                    <p class="mb-2"><?php echo htmlspecialchars($pg[$person['ageKey']] ?? '') ?: '—'; ?></p>
                                    <label class="form-label small text-muted mb-0"><?php echo htmlspecialchars($person['roleLabel']); ?></label>
                                    <p class="mb-2"><?php echo htmlspecialchars($pg[$person['roleKey']] ?? '') ?: '—'; ?></p>
                                    <label class="form-label small text-muted mb-0"><i class='bx bx-phone'></i> Contact</label>
                                    <p class="mb-2"><?php echo htmlspecialchars($pg[$person['contactKey']] ?? '') ?: '—'; ?></p>
                                    <label class="form-label small text-muted mb-0"><i class='bx bx-map-pin'></i> Address</label>
                                    <p class="mb-0"><?php echo htmlspecialchars(AddressService::formatPersonAddress($pg, $person['prefix'])) ?: '—'; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <hr>
                <p class="settings-section-title">Height / Weight</p>
                <?php $h = $profile['health']; ?>
                <?php if(!$h): ?>
                    <p class="text-muted">No health profile recorded.</p>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Height</label><p class="mb-0"><?php echo htmlspecialchars($h['height_cm'] ?? ''); ?> cm</p></div>
                        <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Weight</label><p class="mb-0"><?php echo htmlspecialchars($h['weight_kg'] ?? ''); ?> kg</p></div>
                        <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">BMI</label><p class="mb-0"><?php echo htmlspecialchars($h['bmi'] ?? ''); ?></p></div>
                        <div class="col-md-3 col-sm-6 mb-3"><label class="form-label fw-bold mb-0">Classification</label><p class="mb-0"><?php echo htmlspecialchars($h['bmi_classification'] ?? ''); ?></p></div>
                    </div>
                <?php endif; ?>

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
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_health" type="button">Health</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_reading_level" type="button">Reading Level (<?php echo count($profile['reading_level']); ?>)</button></li>
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

                    <div class="tab-pane fade" id="tab_reading_level">
                        <h6 class="print-section-title fw-bold mb-2">Reading Level Records</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Assessment Date</th><th>Reading Level</th><th>Reading Language</th><th>Remarks</th><th>School Year</th><th>Recorded By</th></tr></thead>
                                <tbody>
                                    <?php if(empty($profile['reading_level'])): ?>
                                        <tr><td colspan="6" class="text-center text-muted">No reading level records.</td></tr>
                                    <?php else: foreach($profile['reading_level'] as $r): ?>
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
    <?php endif; ?>

    <?php if($profile === null): ?>
    <?php
        $masterListQueryBase = 'status=' . urlencode($status_filter);
        if($section_filter !== null){
            $masterListQueryBase .= '&section_id=' . (int) $section_filter;
        }
        if($school_year_filter !== null){
            $masterListQueryBase .= '&school_year_id=' . (int) $school_year_filter;
        }
        if($gender_filter !== null){
            $masterListQueryBase .= '&gender=' . urlencode($gender_filter);
        }
    ?>
    <form action="learner-profile.php" method="get" class="d-flex flex-wrap align-items-end gap-2 mb-3 no-print">
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
        <div>
            <label for="filter_school_year_id" class="form-label mb-0">School Year</label>
            <select class="form-select" id="filter_school_year_id" name="school_year_id" onchange="this.form.submit()">
                <option value="">-- All School Years --</option>
                <?php foreach(($school_years ?? []) as $sy_opt): ?>
                    <option value="<?php echo htmlspecialchars($sy_opt['id']); ?>" <?php echo ($school_year_filter !== null && (int) $school_year_filter === (int) $sy_opt['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sy_opt['school_year']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="filter_section_id" class="form-label mb-0">Section</label>
            <select class="form-select" id="filter_section_id" name="section_id" onchange="this.form.submit()">
                <option value="">-- All Sections --</option>
                <?php foreach(($sections ?? []) as $sec_opt): ?>
                    <option value="<?php echo htmlspecialchars($sec_opt['id']); ?>" <?php echo ($section_filter !== null && (int) $section_filter === (int) $sec_opt['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(($sec_opt['grade_level_name'] ?? '') . ' - ' . $sec_opt['section_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="filter_gender" class="form-label mb-0">Sex</label>
            <select class="form-select" id="filter_gender" name="gender" onchange="this.form.submit()">
                <option value="">-- All --</option>
                <option value="Male" <?php echo ($gender_filter === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($gender_filter === 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>
        <?php if($section_filter !== null || $school_year_filter !== null || $gender_filter !== null): ?>
            <div>
                <a href="learner-profile.php?status=<?php echo htmlspecialchars($status_filter); ?>" class="btn btn-outline-secondary">Clear filters</a>
            </div>
        <?php endif; ?>
    </form>
    <?php if($section_filter !== null || $gender_filter !== null): ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center no-print">
            <span>
                Showing
                <?php if($gender_filter !== null): ?>
                    <strong><?php echo htmlspecialchars($gender_filter); ?></strong>
                <?php endif; ?>
                learners
                <?php if($section_filter !== null): ?>
                    for <strong><?php echo htmlspecialchars($section_filter_label ?? 'the selected section'); ?></strong>
                <?php endif; ?>
                only.
            </span>
            <a href="learner-profile.php?status=<?php echo htmlspecialchars($status_filter); ?>" class="btn btn-sm btn-outline-secondary">Clear filter</a>
        </div>
    <?php endif; ?>
    <div class="card mt-4 no-print">
        <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><?php echo ($section_filter !== null || $gender_filter !== null) ? 'Section Roster' : 'All Learners'; ?></span>
            <div class="btn-group" role="group">
                <a href="?<?php echo str_replace('status=' . urlencode($status_filter), 'status=active', $masterListQueryBase); ?>" class="btn btn-sm <?php echo ($status_filter === 'active') ? 'btn-primary' : 'btn-outline-primary'; ?>">Active</a>
                <a href="?<?php echo str_replace('status=' . urlencode($status_filter), 'status=archived', $masterListQueryBase); ?>" class="btn btn-sm <?php echo ($status_filter === 'archived') ? 'btn-primary' : 'btn-outline-primary'; ?>">Archived</a>
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
                                onclick="window.location.href='learner-profile.php?student_id=<?php echo htmlspecialchars($s['id']); ?>&page=<?php echo $masterPage; ?>&<?php echo htmlspecialchars($masterListQueryBase); ?>'"
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
                <a class="page-link" href="?<?php echo htmlspecialchars($masterListQueryBase); ?>&page=<?php echo $masterPage - 1; ?>">&laquo;</a>
              </li>
              <?php foreach (Paginator::pageWindow($masterPage, $masterPages) as $p): ?>
                <?php if ($p === null): ?>
                  <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                <?php else: ?>
                  <li class="page-item <?php echo $p === $masterPage ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo htmlspecialchars($masterListQueryBase); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
              <li class="page-item <?php echo $masterPage >= $masterPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo htmlspecialchars($masterListQueryBase); ?>&page=<?php echo $masterPage + 1; ?>">&raquo;</a>
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
  <script src="../../../public/js/administrative/learner-profile.js?v=<?php echo filemtime(__DIR__ . '/../../../public/js/administrative/learner-profile.js'); ?>"></script>
</body>
</html>
