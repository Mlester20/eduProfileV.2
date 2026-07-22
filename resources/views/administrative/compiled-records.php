<?php
require_once __DIR__ . '/../../../app/controllers/administrative/compiledrecordscontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['administrative']);

/**
 * One short summary string per category so the main table stays scannable
 * instead of growing a column per category's full field set.
 */
function compiledRecordSummary($category, $record){
    switch($category){
        case 'Academic':
            return ($record['subject_name'] ?? '') . ' — ' . ($record['grade'] ?? '');
        case 'Behavioral':
            return $record['category'] ?? '';
        case 'Developmental':
            return $record['domain'] ?? '';
        case 'Health':
            return $record['bmi_classification'] ?? '';
        case 'Attendance':
            return ($record['attendance_date'] ?? '') . ' — ' . ($record['status'] ?? '');
        case 'Achievements':
            return $record['title'] ?? '';
        default:
            return '';
    }
}

// Full field list per category, shown in the "View" modal rather than as
// individual table columns.
$categoryFieldLabels = [
    'Academic' => [
        'subject_name' => 'Subject',
        'grading_period' => 'Grading Period',
        'grade' => 'Grade',
        'remarks' => 'Remarks',
    ],
    'Behavioral' => [
        'observation_date' => 'Observation Date',
        'category' => 'Category',
        'observation' => 'Observation',
        'intervention' => 'Intervention',
        'remarks' => 'Remarks',
    ],
    'Developmental' => [
        'domain' => 'Domain',
        'observation' => 'Observation',
        'recommendation' => 'Recommendation',
    ],
    'Health' => [
        'height_cm' => 'Height (cm)',
        'weight_kg' => 'Weight (kg)',
        'bmi' => 'BMI',
        'bmi_classification' => 'BMI Classification',
        'blood_type' => 'Blood Type',
        'allergies' => 'Allergies',
    ],
    'Attendance' => [
        'attendance_date' => 'Date',
        'session' => 'Session',
        'status' => 'Status',
        'remarks' => 'Remarks',
    ],
    'Achievements' => [
        'title' => 'Title',
        'level' => 'Level',
        'category' => 'Category',
        'date_received' => 'Date Received',
        'awarding_body' => 'Awarding Body',
    ],
][$category] ?? [];
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

    <form action="compiled-records.php" method="get" class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <div>
            <label for="category" class="form-label mb-0">Category</label>
            <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                <?php foreach(CompiledRecordsController::CATEGORIES as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
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
        <div>
            <label for="section_id" class="form-label mb-0">Section</label>
            <select class="form-select" id="section_id" name="section_id" onchange="this.form.submit()">
                <option value="">-- All Sections --</option>
                <?php foreach(($sections ?? []) as $section): ?>
                    <option value="<?php echo htmlspecialchars($section['id']); ?>" <?php echo ($section_filter !== null && (int) $section_filter === (int) $section['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(($section['grade_level_name'] ?? '') . ' - ' . $section['section_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <div class="card mt-4">
        <h5 class="card-header">Compiled Records &mdash; <?php echo htmlspecialchars($category); ?></h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Section</th>
                        <th>School Year</th>
                        <th><?php echo htmlspecialchars($category); ?> Summary</th>
                        <th>Assigned Teacher</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(($compiledRecords ?? []) as $index => $record): ?>
                        <?php
                            $jsRecord = $record;
                            $jsRecord['student_name'] = trim($record['student_first_name'] . ' ' . ($record['student_middle_name'] ?? '') . ' ' . $record['student_last_name'] . ' ' . ($record['student_suffix'] ?? ''));
                            $jsRecord['section_display'] = trim(($record['grade_name'] ?? '') . ' - ' . ($record['section_name'] ?? ''));
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($jsRecord['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($jsRecord['section_display']); ?></td>
                            <td><?php echo htmlspecialchars($record['school_year'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(compiledRecordSummary($category, $record)); ?></td>
                            <td><?php echo htmlspecialchars($record['assigned_teacher_name'] ?? ''); ?></td>
                            <td>
                                <span class="badge bg-label-<?php echo ($record['student_status'] ?? 'active') === 'archived' ? 'secondary' : 'success'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($record['student_status'] ?? 'active')); ?>
                                </span>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewRecordModal"
                                    onclick='viewCompiledRecord(<?php echo json_encode($jsRecord, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'
                                >
                                    <i class="bx bx-show"></i> View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($compiledRecords)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No records found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- view modal (shared, repopulated per row via viewCompiledRecord()) -->
    <div class="modal fade" id="viewRecordModal" tabindex="-1" aria-labelledby="viewRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRecordModalLabel"><?php echo htmlspecialchars($category); ?> Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr><th class="text-muted" style="width:35%">Student Name</th><td id="viewStudentName"></td></tr>
                            <tr><th class="text-muted">Section</th><td id="viewSection"></td></tr>
                            <tr><th class="text-muted">School Year</th><td id="viewSchoolYear"></td></tr>
                            <tr><th class="text-muted">Recorded By</th><td id="viewRecordedBy"></td></tr>
                            <tr><th class="text-muted">Assigned Teacher</th><td id="viewAssignedTeacher"></td></tr>
                        </tbody>
                        <tbody id="viewRecordDetails"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
  <script>
    const categoryFieldLabels = <?php echo json_encode($categoryFieldLabels); ?>;

    function viewCompiledRecord(record){
        document.getElementById('viewStudentName').textContent = record.student_name || '';
        document.getElementById('viewSection').textContent = record.section_display || '';
        document.getElementById('viewSchoolYear').textContent = record.school_year || '';
        document.getElementById('viewRecordedBy').textContent = record.recorded_by_name || '';
        document.getElementById('viewAssignedTeacher').textContent = record.assigned_teacher_name || '';

        const detailsBody = document.getElementById('viewRecordDetails');
        detailsBody.innerHTML = '';
        for(const key in categoryFieldLabels){
            const row = document.createElement('tr');
            const th = document.createElement('th');
            th.className = 'text-muted';
            th.textContent = categoryFieldLabels[key];
            const td = document.createElement('td');
            td.textContent = record[key] ?? '';
            row.appendChild(th);
            row.appendChild(td);
            detailsBody.appendChild(row);
        }
    }
  </script>
</body>
</html>
