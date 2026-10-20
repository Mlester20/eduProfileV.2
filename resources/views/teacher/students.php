<?php
require_once __DIR__ . '/../../../app/controllers/teacher/StudentsController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../app/helpers/StudentsAge.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

if(isset($_GET['export']) && $_GET['export'] === 'xlsx'){
    require_once __DIR__ . '/../../../app/services/StudentExportService.php';
    StudentExportService::exportXlsx($controller->getAllForExport(), $parent_guardian_by_student);
    exit();
}

$religionOptions = [
    'Roman Catholic', 'Islam', 'Iglesia ni Cristo', 'Born Again Christian',
    'Seventh-day Adventist', "Jehovah's Witness", 'Baptist', 'United Methodist',
    'Aglipayan (Philippine Independent Church)'
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
  <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Students</title>
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
    .bmi-badge { font-weight: 600; padding: .25em .6em; border-radius: .375rem; display: inline-block; }
    .bmi-badge-severely-wasted { background-color: #f8d7da; color: #842029; }
    .bmi-badge-wasted          { background-color: #fff3cd; color: #997404; }
    .bmi-badge-normal          { background-color: #d1e7dd; color: #0f5132; }
    .bmi-badge-overweight      { background-color: #fff3cd; color: #997404; }
    .bmi-badge-obese           { background-color: #f8d7da; color: #842029; }
    #studentModal .nav-link[disabled] { pointer-events: none; opacity: .5; }
    #student_picture_preview { width: 90px; height: 90px; object-fit: cover; border-radius: .375rem; border: 1px solid #d9dee3; }
  </style>
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <div class="text-end">
      <a href="students-print.php" target="_blank" class="btn btn-outline-secondary me-2">
        <i class="bx bx-printer"></i> Print
      </a>
      <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bx bx-import"></i> Import / Export
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="?export=xlsx"><i class="bx bx-download me-2"></i>Export to Excel</a></li>
          <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#importStudentsModal"><i class="bx bx-upload me-2"></i>Import from Excel</a></li>
        </ul>
      </div>
      <button class="btn btn-primary" type="button" onclick="openStudentModal(null)">
        <i class="bx bx-plus"></i> Add Student
      </button>
    </div>

    <!-- import modal -->
    <div class="modal fade" id="importStudentsModal" tabindex="-1" aria-labelledby="importStudentsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importStudentsLabel">Import Students from Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex flex-column align-items-center">
                            <span class="avatar avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; flex: 0 0 28px; font-weight: 600;">1</span>
                            <div style="width: 2px; background: #d9dee3;" class="flex-grow-1 my-1"></div>
                        </div>
                        <div class="ms-3 mb-4 flex-grow-1">
                            <h6 class="mb-1">Download the template</h6>
                            <p class="text-muted small mb-2">Fill in one row per student. Every column has a sample value in row 2. Or skip this step and upload an SF1 School Register export from LIS directly in step 2.</p>
                            <a href="../../../app/controllers/teacher/StudentImportController.php?template=1" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-download"></i> Download Template
                            </a>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="d-flex flex-column align-items-center">
                            <span class="avatar avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; flex: 0 0 28px; font-weight: 600;">2</span>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="mb-3">Upload the filled-in template or an SF1 export</h6>

                            <form action="../../../app/controllers/teacher/StudentImportController.php" method="post" enctype="multipart/form-data">
                                <?= Csrf::field() ?>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="import_file" class="form-label">Excel File (.xlsx or SF1 .xls)</label>
                                            <input class="form-control" type="file" name="import_file" id="import_file" accept=".xlsx,.xls" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">School Year</label>
                                            <?php if (!empty($school_years)): ?>
                                                <input class="form-control" type="text" value="<?php echo htmlspecialchars($school_years[0]['school_year']); ?>" disabled>
                                                <input type="hidden" name="school_year_id" value="<?php echo htmlspecialchars($school_years[0]['id']); ?>">
                                            <?php else: ?>
                                                <input class="form-control" type="text" value="No active school year set" disabled>
                                                <div class="form-text text-danger">Contact an admin to set an active school year before importing students.</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Grade Level & Section</label>
                                            <p class="form-text text-muted mt-0 mb-2">All imported students will be assigned to this section.</p>
                                            <?php if (count($my_sections ?? []) === 1): ?>
                                                <?php $my_section = $my_sections[0]; ?>
                                                <input
                                                    class="form-control"
                                                    type="text"
                                                    value="<?php echo htmlspecialchars($my_section['grade_level_name'] . ' - ' . $my_section['section_name']); ?>"
                                                    disabled
                                                >
                                                <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($my_section['id']); ?>">
                                                <input type="hidden" name="grade_level_id" value="<?php echo htmlspecialchars($my_section['grade_level_id']); ?>">
                                            <?php elseif (count($my_sections ?? []) > 1): ?>
                                                <select class="form-select" name="section_id" id="import_section_id">
                                                    <option value="" selected disabled>-- Choose Section --</option>
                                                    <?php foreach ($my_sections as $section): ?>
                                                        <option
                                                            value="<?php echo htmlspecialchars($section['id']); ?>"
                                                            data-grade-level-id="<?php echo htmlspecialchars($section['grade_level_id']); ?>"
                                                        >
                                                            <?php echo htmlspecialchars($section['grade_level_name'] . ' - ' . $section['section_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="hidden" name="grade_level_id" id="import_grade_level_id">
                                            <?php else: ?>
                                                <input class="form-control" type="text" value="No section assigned to you yet" disabled>
                                                <div class="form-text text-danger">Contact an admin to get assigned as a section adviser before importing students.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer px-0 pb-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="import_students">
                                        <i class="bx bx-upload"></i> Upload & Import
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- student modal: single Add/Edit modal, everything else lives in tabs -->
    <div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="studentModalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-btn-info" data-bs-toggle="tab" data-bs-target="#tab-info" type="button" role="tab">Student Info</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-btn-parent" data-bs-toggle="tab" data-bs-target="#tab-parent" type="button" role="tab" disabled>Parent/Guardian</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-btn-health" data-bs-toggle="tab" data-bs-target="#tab-health" type="button" role="tab" disabled>Health Profile</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-btn-reading" data-bs-toggle="tab" data-bs-target="#tab-reading" type="button" role="tab" disabled>Reading Level</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-btn-academic" data-bs-toggle="tab" data-bs-target="#tab-academic" type="button" role="tab" disabled>Academic Records</button>
                        </li>
                    </ul>
                    <div class="alert alert-info py-2" id="studentModalHint">Save the student's basic info first to unlock the other tabs.</div>

                    <div class="tab-content" id="studentModalTabContent">

                        <!-- Student Info -->
                        <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                            <form id="studentInfoForm" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="student_id_field">
                                <div class="row g-3">
                                    <div class="col-md-3 mb-3 d-flex flex-column align-items-start">
                                        <label class="form-label">Picture</label>
                                        <img id="student_picture_preview" src="../../../public/assets/img/avatars/1.png" alt="Picture preview" class="mb-2">
                                        <input class="form-control form-control-sm" type="file" name="picture" id="picture" accept="image/jpeg,image/png,image/gif">
                                        <div class="form-text">JPG, PNG, or GIF. Max 2MB.</div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="lrn" class="form-label">LRN</label>
                                                <input
                                                    class="form-control"
                                                    type="text"
                                                    name="lrn"
                                                    id="lrn"
                                                    placeholder="e.g., 123456789012"
                                                    inputmode="numeric"
                                                    pattern="\d{12}"
                                                    maxlength="12"
                                                    title="LRN must be exactly 12 digits, numbers only"
                                                    required
                                                >
                                                <div class="invalid-feedback" id="lrn_feedback">LRN must be exactly 12 digits, numbers only.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="birth_date" class="form-label">Birth Date</label>
                                                <input class="form-control" type="date" name="birth_date" id="birth_date" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input class="form-control" type="text" name="first_name" id="first_name" placeholder="e.g., Juan" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input class="form-control" type="text" name="middle_name" id="middle_name" placeholder="e.g., Liwaliw">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input class="form-control" type="text" name="last_name" id="last_name" placeholder="e.g., Dela Cruz" required>
                                    </div>
                                    <div class="col-md-1 mb-3">
                                        <label for="suffix" class="form-label">Suffix</label>
                                        <input class="form-control" type="text" name="suffix" id="suffix" placeholder="Jr.">
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select" name="gender" id="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="age_as_of_june" class="form-label">Age (as of June)</label>
                                        <input class="form-control" type="number" min="0" max="99" name="age_as_of_june" id="age_as_of_june" placeholder="e.g., 7">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="mother_tongue" class="form-label">Mother Tongue</label>
                                        <input class="form-control" type="text" name="mother_tongue" id="mother_tongue" placeholder="e.g., Tagalog">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="ip_ethnic_group" class="form-label">IP / Ethnic Group</label>
                                        <input class="form-control" type="text" name="ip_ethnic_group" id="ip_ethnic_group" placeholder="e.g., Ilocano">
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="religion" class="form-label">Religion</label>
                                        <select class="form-select" name="religion" id="religion">
                                            <option value="">Select Religion</option>
                                            <?php foreach($religionOptions as $option): ?>
                                                <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                                            <?php endforeach; ?>
                                            <option value="__other__">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 d-none" id="religion_other_wrapper">
                                        <label for="religion_other" class="form-label">Specify Religion</label>
                                        <input class="form-control" type="text" id="religion_other" placeholder="Enter religion">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="learning_modality" class="form-label">Learning Modality</label>
                                        <select class="form-select" name="learning_modality" id="learning_modality">
                                            <option value="">Select learning modality</option>
                                            <option value="Face-to-Face">Face-to-Face</option>
                                            <option value="Modular Distance Learning">Modular Distance Learning</option>
                                            <option value="Online Distance Learning">Online Distance Learning</option>
                                            <option value="Blended Learning">Blended Learning</option>
                                            <option value="Homeschooling">Homeschooling</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="province" class="form-label">Province</label>
                                        <select class="form-select" name="province" id="province">
                                            <option value="">Loading provinces...</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="province_other" placeholder="Enter province">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="city_municipality" class="form-label">City / Municipality</label>
                                        <select class="form-select" name="city_municipality" id="city_municipality" disabled>
                                            <option value="">Select province first</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="city_municipality_other" placeholder="Enter city/municipality">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="barangay" class="form-label">Barangay</label>
                                        <select class="form-select" name="barangay" id="barangay" disabled>
                                            <option value="">Select city/municipality first</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="barangay_other" placeholder="Enter barangay">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="house_number" class="form-label">House #</label>
                                        <input class="form-control" type="text" name="house_number" id="house_number">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="street" class="form-label">Street</label>
                                        <input class="form-control" type="text" name="street" id="street">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sitio" class="form-label">Sitio</label>
                                        <input class="form-control" type="text" name="sitio" id="sitio">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="purok" class="form-label">Purok</label>
                                        <input class="form-control" type="text" name="purok" id="purok">
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-12 mb-3">
                                        <label for="remarks" class="form-label">Remarks</label>
                                        <input class="form-control" type="text" name="remarks" id="remarks" placeholder="e.g., LWD, Transferred In">
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">School Year</label>
                                        <?php if (!empty($school_years)): ?>
                                            <input class="form-control" type="text" value="<?php echo htmlspecialchars($school_years[0]['school_year']); ?>" disabled>
                                            <input type="hidden" name="school_year_id" id="school_year_id" value="<?php echo htmlspecialchars($school_years[0]['id']); ?>">
                                        <?php else: ?>
                                            <input class="form-control" type="text" value="No active school year set" disabled>
                                            <div class="form-text text-danger">Contact an admin to set an active school year before adding students.</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Grade Level & Section</label>
                                        <?php if (count($my_sections ?? []) === 1): ?>
                                            <?php $my_section = $my_sections[0]; ?>
                                            <input class="form-control" type="text" value="<?php echo htmlspecialchars($my_section['grade_level_name'] . ' - ' . $my_section['section_name']); ?>" disabled>
                                            <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($my_section['id']); ?>">
                                            <input type="hidden" name="grade_level_id" value="<?php echo htmlspecialchars($my_section['grade_level_id']); ?>">
                                        <?php elseif (count($my_sections ?? []) > 1): ?>
                                            <select class="form-select" name="section_id" id="section_id">
                                                <option value="" selected disabled>-- Choose Section --</option>
                                                <?php foreach ($my_sections as $section): ?>
                                                    <option
                                                        value="<?php echo htmlspecialchars($section['id']); ?>"
                                                        data-grade-level-id="<?php echo htmlspecialchars($section['grade_level_id']); ?>"
                                                    >
                                                        <?php echo htmlspecialchars($section['grade_level_name'] . ' - ' . $section['section_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="hidden" name="grade_level_id" id="grade_level_id">
                                        <?php else: ?>
                                            <input class="form-control" type="text" value="No section assigned to you yet" disabled>
                                            <div class="form-text text-danger">Contact an admin to get assigned as a section adviser before adding students.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary" id="studentInfoSaveBtn">Save Student Info</button>
                                </div>
                            </form>
                        </div>

                        <!-- Parent/Guardian -->
                        <div class="tab-pane fade" id="tab-parent" role="tabpanel">
                            <form id="parentGuardianForm">

                                <h6 class="fw-bold mb-2">Father</h6>
                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_father_name" class="form-label">Name</label>
                                        <input class="form-control" type="text" id="pg_father_name">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_father_occupation" class="form-label">Occupation</label>
                                        <input class="form-control" type="text" id="pg_father_occupation">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="pg_father_age" class="form-label">Age</label>
                                        <input class="form-control" type="number" min="0" max="120" id="pg_father_age">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="pg_father_contact" class="form-label">Contact</label>
                                        <input class="form-control" type="text" id="pg_father_contact">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_father_province" class="form-label">Province</label>
                                        <select class="form-select" id="pg_father_province">
                                            <option value="">Loading provinces...</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_father_province_other" placeholder="Enter province">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_father_city_municipality" class="form-label">City / Municipality</label>
                                        <select class="form-select" id="pg_father_city_municipality" disabled>
                                            <option value="">Select province first</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_father_city_municipality_other" placeholder="Enter city/municipality">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_father_barangay" class="form-label">Barangay</label>
                                        <select class="form-select" id="pg_father_barangay" disabled>
                                            <option value="">Select city/municipality first</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_father_barangay_other" placeholder="Enter barangay">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_father_house_number" class="form-label">House #</label>
                                        <input class="form-control" type="text" id="pg_father_house_number">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_father_street" class="form-label">Street</label>
                                        <input class="form-control" type="text" id="pg_father_street">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_father_sitio" class="form-label">Sitio</label>
                                        <input class="form-control" type="text" id="pg_father_sitio">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_father_purok" class="form-label">Purok</label>
                                        <input class="form-control" type="text" id="pg_father_purok">
                                    </div>
                                </div>

                                <hr>
                                <h6 class="fw-bold mb-2">Mother</h6>
                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_mother_name" class="form-label">Name</label>
                                        <input class="form-control" type="text" id="pg_mother_name">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_mother_occupation" class="form-label">Occupation</label>
                                        <input class="form-control" type="text" id="pg_mother_occupation">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="pg_mother_age" class="form-label">Age</label>
                                        <input class="form-control" type="number" min="0" max="120" id="pg_mother_age">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="pg_mother_contact" class="form-label">Contact</label>
                                        <input class="form-control" type="text" id="pg_mother_contact">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_mother_province" class="form-label">Province</label>
                                        <select class="form-select" id="pg_mother_province">
                                            <option value="">Loading provinces...</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_mother_province_other" placeholder="Enter province">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_mother_city_municipality" class="form-label">City / Municipality</label>
                                        <select class="form-select" id="pg_mother_city_municipality" disabled>
                                            <option value="">Select province first</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_mother_city_municipality_other" placeholder="Enter city/municipality">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_mother_barangay" class="form-label">Barangay</label>
                                        <select class="form-select" id="pg_mother_barangay" disabled>
                                            <option value="">Select city/municipality first</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_mother_barangay_other" placeholder="Enter barangay">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_mother_house_number" class="form-label">House #</label>
                                        <input class="form-control" type="text" id="pg_mother_house_number">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_mother_street" class="form-label">Street</label>
                                        <input class="form-control" type="text" id="pg_mother_street">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_mother_sitio" class="form-label">Sitio</label>
                                        <input class="form-control" type="text" id="pg_mother_sitio">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_mother_purok" class="form-label">Purok</label>
                                        <input class="form-control" type="text" id="pg_mother_purok">
                                    </div>
                                </div>

                                <hr>
                                <h6 class="fw-bold mb-2">Guardian</h6>
                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_guardian_name" class="form-label">Name</label>
                                        <input class="form-control" type="text" id="pg_guardian_name">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_guardian_relationship" class="form-label">Relationship</label>
                                        <input class="form-control" type="text" id="pg_guardian_relationship">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="pg_guardian_age" class="form-label">Age</label>
                                        <input class="form-control" type="number" min="0" max="120" id="pg_guardian_age">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="pg_guardian_contact" class="form-label">Contact</label>
                                        <input class="form-control" type="text" id="pg_guardian_contact">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_guardian_province" class="form-label">Province</label>
                                        <select class="form-select" id="pg_guardian_province">
                                            <option value="">Loading provinces...</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_guardian_province_other" placeholder="Enter province">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_guardian_city_municipality" class="form-label">City / Municipality</label>
                                        <select class="form-select" id="pg_guardian_city_municipality" disabled>
                                            <option value="">Select province first</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_guardian_city_municipality_other" placeholder="Enter city/municipality">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pg_guardian_barangay" class="form-label">Barangay</label>
                                        <select class="form-select" id="pg_guardian_barangay" disabled>
                                            <option value="">Select city/municipality first</option>
                                        </select>
                                        <input class="form-control mt-1 d-none" type="text" id="pg_guardian_barangay_other" placeholder="Enter barangay">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_guardian_house_number" class="form-label">House #</label>
                                        <input class="form-control" type="text" id="pg_guardian_house_number">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_guardian_street" class="form-label">Street</label>
                                        <input class="form-control" type="text" id="pg_guardian_street">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_guardian_sitio" class="form-label">Sitio</label>
                                        <input class="form-control" type="text" id="pg_guardian_sitio">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pg_guardian_purok" class="form-label">Purok</label>
                                        <input class="form-control" type="text" id="pg_guardian_purok">
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Save Parent/Guardian Info</button>
                                </div>
                            </form>
                        </div>

                        <!-- Health Profile -->
                        <div class="tab-pane fade" id="tab-health" role="tabpanel">
                            <form id="healthProfileForm">
                                <div class="row g-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="hp_height_cm" class="form-label">Height (cm)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="hp_height_cm">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="hp_weight_kg" class="form-label">Weight (kg)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="hp_weight_kg">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="hp_bmi" class="form-label">BMI</label>
                                        <input type="text" class="form-control" id="hp_bmi" readonly>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label d-block">Classification</label>
                                        <span class="bmi-badge" id="hp_bmi_classification_badge">&mdash;</span>
                                        <input type="hidden" id="hp_bmi_classification">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="hp_blood_type" class="form-label">Blood Type</label>
                                        <select class="form-select" id="hp_blood_type">
                                            <option value="">Unknown</option>
                                            <?php foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt): ?>
                                                <option value="<?= $bt ?>"><?= $bt ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="hp_vision_screening_result" class="form-label">Vision Screening Result</label>
                                        <select class="form-select" id="hp_vision_screening_result">
                                            <option value="">-- Select --</option>
                                            <option value="Normal">Normal</option>
                                            <option value="With Problem (Needs Referral)">With Problem (Needs Referral)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="hp_hearing_screening_result" class="form-label">Hearing Screening Result</label>
                                        <select class="form-select" id="hp_hearing_screening_result">
                                            <option value="">-- Select --</option>
                                            <option value="Normal">Normal</option>
                                            <option value="With Problem (Needs Referral)">With Problem (Needs Referral)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="hp_allergies" class="form-label">Allergies</label>
                                        <textarea class="form-control" id="hp_allergies" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="hp_has_medical_condition" class="form-label">Medical Conditions</label>
                                        <select class="form-select" id="hp_has_medical_condition">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8 mb-3 d-none" id="hp_medical_conditions_wrapper">
                                        <label for="hp_medical_conditions" class="form-label">Specify</label>
                                        <textarea class="form-control" id="hp_medical_conditions" rows="2"></textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="hp_immunization_status" class="form-label">Immunization Status</label>
                                        <textarea class="form-control" id="hp_immunization_status" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Save Health Profile</button>
                                </div>
                            </form>
                        </div>

                        <!-- Reading Level -->
                        <div class="tab-pane fade" id="tab-reading" role="tabpanel">
                            <form id="readingLevelForm" class="border rounded p-3 mb-3 bg-light">
                                <input type="hidden" id="rl_edit_id">
                                <div class="row g-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="rl_assessment_date" class="form-label">Assessment Date</label>
                                        <input type="date" class="form-control" id="rl_assessment_date" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="rl_reading_level" class="form-label">Reading Level</label>
                                        <select class="form-select" id="rl_reading_level" required>
                                            <option value="" selected disabled>Select reading level</option>
                                            <option value="Non-reader">Non-reader</option>
                                            <option value="Frustration">Frustration</option>
                                            <option value="Instructional">Instructional</option>
                                            <option value="Independent">Independent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="rl_reading_language" class="form-label">Reading Language</label>
                                        <select class="form-select" id="rl_reading_language" required>
                                            <option value="" selected disabled>Select reading language</option>
                                            <option value="English">English</option>
                                            <option value="Filipino">Filipino</option>
                                            <option value="MTB">MTB</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary flex-grow-1" id="rl_submit_btn">Add</button>
                                        <button type="button" class="btn btn-outline-secondary d-none" id="rl_cancel_btn">Cancel</button>
                                    </div>
                                    <div class="col-12 mb-1">
                                        <label for="rl_remarks" class="form-label">Remarks</label>
                                        <textarea class="form-control" id="rl_remarks" rows="2"></textarea>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Level</th>
                                            <th>Language</th>
                                            <th>Remarks</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="readingLevelList">
                                        <tr><td colspan="5" class="text-center text-muted">No reading level records yet.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Academic Records -->
                        <div class="tab-pane fade" id="tab-academic" role="tabpanel">
                            <form id="academicRecordForm" class="border rounded p-3 mb-3 bg-light">
                                <input type="hidden" id="ap_edit_id">
                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="ap_subject_name" class="form-label">Subject Name</label>
                                        <input class="form-control" type="text" id="ap_subject_name" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="ap_grading_period" class="form-label">Grading Period</label>
                                        <select class="form-select" id="ap_grading_period" required>
                                            <option value="" selected disabled>-- Choose --</option>
                                            <option value="1st Quarter">1st Quarter</option>
                                            <option value="2nd Quarter">2nd Quarter</option>
                                            <option value="3rd Quarter">3rd Quarter</option>
                                            <option value="4th Quarter">4th Quarter</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="ap_grade" class="form-label">Grade</label>
                                        <input class="form-control" type="number" step="0.01" min="0" max="100" id="ap_grade" required>
                                    </div>
                                    <div class="col-md-3 mb-3 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary flex-grow-1" id="ap_submit_btn">Add</button>
                                        <button type="button" class="btn btn-outline-secondary d-none" id="ap_cancel_btn">Cancel</button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-n2 mb-2">Remarks is set automatically from the grade -- 75 and above is Passed, 74 and below is Failed.</small>
                            </form>
                            <div class="d-flex justify-content-end mb-2">
                                <div style="max-width: 220px; width: 100%;">
                                    <select class="form-select form-select-sm" id="ap_period_filter">
                                        <option value="">All Grading Periods</option>
                                        <option value="1st Quarter">1st Quarter</option>
                                        <option value="2nd Quarter">2nd Quarter</option>
                                        <option value="3rd Quarter">3rd Quarter</option>
                                        <option value="4th Quarter">4th Quarter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Grading Period</th>
                                            <th>Grade</th>
                                            <th>Remarks</th>
                                            <th>Teacher</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="academicRecordList">
                                        <tr><td colspan="6" class="text-center text-muted">No academic records yet.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Manage Students</h5>
        <div class="position-relative" style="max-width: 320px; width: 100%;">
            <input
                type="text"
                class="form-control"
                id="student_table_search"
                placeholder="Search by name or LRN..."
                autocomplete="off"
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
            >
        </div>
      </div>
        <div class="table-responsive nowrap">
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Picture</th>
                <th>LRN</th>
                <th>Student Name</th>
                <th>Grade Level & Section</th>
                <th>Age</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $rows        = $students['data']         ?? [];
                $currentPage = $students['current_page'] ?? 1;
                $totalPages  = $students['total_pages']  ?? 1;
                $perPage     = $students['per_page']     ?? 10;
                $offset      = ($currentPage - 1) * $perPage;
              ?>
              <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $index => $student): ?>
                  <tr role="button" style="cursor: pointer;" onclick="openStudentModal(<?= (int) $student['id'] ?>)">
                    <td><?php echo $offset + $index + 1; ?></td>
                    <td>
                      <?php if (!empty($student['picture'])): ?>
                        <img src="../../../<?= htmlspecialchars($student['picture']) ?>" alt="" style="width: 36px; height: 36px; object-fit: cover; border-radius: .375rem;">
                      <?php else: ?>
                        <i class="bx bx-user-circle fs-3 text-muted"></i>
                      <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($student['lrn']); ?></td>
                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['grade_name'] . ' - ' . $student['section_name']); ?></td>
                    <td><?php echo StudentsAge::calculateAge($student['birth_date']); ?></td>
                    <td>
                      <a
                        href="student-print.php?id=<?php echo $student['id']; ?>"
                        target="_blank"
                        class="btn btn-sm btn-outline-primary"
                        title="Print"
                        onclick="event.stopPropagation();"
                      >
                        <i class="bx bx-printer"></i>
                      </a>

                      <button
                        class="btn btn-sm btn-warning"
                        type="button"
                        title="Edit"
                        onclick="event.stopPropagation(); openStudentModal(<?= (int) $student['id'] ?>)"
                      >
                        <i class="bx bx-edit"></i>
                      </button>

                      <form action="../../../app/controllers/teacher/StudentsController.php" method="post" class="d-inline">
                          <?= Csrf::field() ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
                        <button
                          type="submit"
                          class="btn btn-sm btn-danger"
                          name="delete_student"
                          title="Delete"
                          onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this student? this action cannot be undone.');"
                        >
                          <i class="bx bx-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center">No students found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php $studentsQuery = isset($_GET['search']) && trim($_GET['search']) !== '' ? 'search=' . urlencode(trim($_GET['search'])) . '&' : ''; ?>
        <?php if ($totalPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $studentsQuery; ?>page=<?php echo $currentPage - 1; ?>">&laquo;</a>
              </li>
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?<?php echo $studentsQuery; ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo $studentsQuery; ?>page=<?php echo $currentPage + 1; ?>">&raquo;</a>
              </li>
            </ul>
          </nav>
        </div>
        <?php endif; ?>
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
    <script src="../../../public/js/teacher/home.js"></script>
    <script src="../../../public/js/teacher/students.js?v=<?php echo filemtime(__DIR__ . '/../../../public/js/teacher/students.js'); ?>"></script>
</body>
</html>
