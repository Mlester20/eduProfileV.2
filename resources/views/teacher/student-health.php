<?php
session_start();
require_once __DIR__ . '/../../../app/controllers/teacher/StudentHealthController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['teacher']);

$controller = new StudentHealthController($con);

$method = $_SERVER['REQUEST_METHOD'];
$isAjax = (
    $method !== 'GET' ||
    (isset($_GET['ajax']) && $_GET['ajax'] == '1') ||
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
);

if($isAjax){
    header('Content-Type: application/json');

    switch($method){
        case 'GET':
            if(isset($_GET['id'])){
                $record = $controller->getById((int) $_GET['id']);
                echo json_encode(['success' => (bool) $record, 'data' => $record]);
            }else{
                $student_id = isset($_GET['student_id']) ? (int) $_GET['student_id'] : null;
                echo json_encode(['success' => true, 'data' => $controller->index($student_id)['data']]);
            }
            break;

        case 'POST':
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            if(!is_array($data)){
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
                break;
            }
            Csrf::requireValidJson($data['csrf_token'] ?? null);
            echo json_encode($controller->create($data));
            break;

        case 'PUT':
        case 'PATCH':
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            if(!is_array($data) || !isset($data['id'])){
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
                break;
            }
            Csrf::requireValidJson($data['csrf_token'] ?? null);
            echo json_encode($controller->update((int) $data['id'], $data));
            break;

        case 'DELETE':
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            $id = isset($data['id']) ? (int) $data['id'] : (isset($_GET['id']) ? (int) $_GET['id'] : null);
            if(!$id){
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing record id.']);
                break;
            }
            Csrf::requireValidJson($data['csrf_token'] ?? null);
            echo json_encode($controller->delete($id));
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }
    exit();
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$health_records = $controller->index(null, $page);
$students = $controller->getStudents();
$students_without_profile = $controller->getStudentsWithoutHealthProfile();
$active_sy = $controller->getActiveSy();
$active_school_year_id = !empty($active_sy) ? $active_sy[0]['id'] : null;
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Student Health Profiles</title>
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
  </style>
</head>
<body>

    <?php FlashMessage::showFlash(); ?>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <div class="text-end">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHealthProfileModal">Record Health Profile</button>
    </div>

    <!-- add modal -->
    <div class="modal fade" id="createHealthProfileModal" tabindex="-1" aria-labelledby="createHealthProfileLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createHealthProfileLabel">Record Health Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createHealthProfileForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="student_id_search" class="form-label">Student</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="student_id_search" placeholder="Search student..." autocomplete="off">
                                    <div id="student_id_suggestions" class="list-group shadow-sm position-absolute w-100" style="top: 100%; left: 0; z-index: 5; max-height: 200px; overflow-y: auto; display: none;"></div>
                                </div>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="">Select student</option>
                                    <?php if(!empty($students_without_profile)): ?>
                                        <?php foreach($students_without_profile as $student): ?>
                                            <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']); ?>
                                            <option value="<?= htmlspecialchars($student['id']); ?>"><?= htmlspecialchars($studentName); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if(empty($students_without_profile)): ?>
                                    <div class="form-text text-warning">Every student under your advisory already has a health profile.</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_year_id" class="form-label">School Year</label>
                                <select class="form-select" id="school_year_id" name="school_year_id" required>
                                    <option value="">Select school year</option>
                                    <?php if(!empty($active_sy)): ?>
                                        <?php foreach($active_sy as $sy): ?>
                                            <option value="<?= htmlspecialchars($sy['id']); ?>"><?= htmlspecialchars($sy['school_year']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="height_cm" class="form-label">Height (cm)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="height_cm" name="height_cm" placeholder="e.g., 165" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="weight_kg" class="form-label">Weight (kg)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="weight_kg" name="weight_kg" placeholder="e.g., 25" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="bmi" class="form-label">BMI</label>
                                <input type="text" class="form-control" id="bmi" name="bmi" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label d-block">Classification</label>
                                <span class="bmi-badge" id="bmi_classification_badge">&mdash;</span>
                                <input type="hidden" id="bmi_classification" name="bmi_classification">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Blood Type</label>
                                <select class="form-select" id="blood_type" name="blood_type">
                                    <option value="">Unknown</option>
                                    <?php foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt): ?>
                                        <option value="<?= $bt ?>"><?= $bt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vision_screening_result" class="form-label">Vision Screening Result</label>
                                <input type="text" class="form-control" id="vision_screening_result" name="vision_screening_result" placeholder="e.g,. Normal">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hearing_screening_result" class="form-label">Hearing Screening Result</label>
                                <input type="text" class="form-control" id="hearing_screening_result" name="hearing_screening_result" placeholder="e.g., Normal">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="allergies" name="allergies" rows="2" placeholder="e.g., Seafoods"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="medical_conditions" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="2" placeholder="e.g., Normal"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="immunization_status" class="form-label">Immunization Status</label>
                                <textarea class="form-control" id="immunization_status" name="immunization_status" rows="2" placeholder="e.g., Normal"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- edit modal -->
    <div class="modal fade" id="editHealthProfileModal" tabindex="-1" aria-labelledby="editHealthProfileLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHealthProfileLabel">Edit Health Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editHealthProfileForm">
                    <input type="hidden" name="id" id="edit_health_profile_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_student_id_search" class="form-label">Student</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="edit_student_id_search" placeholder="Search student..." autocomplete="off">
                                    <div id="edit_student_id_suggestions" class="list-group shadow-sm position-absolute w-100" style="top: 100%; left: 0; z-index: 5; max-height: 200px; overflow-y: auto; display: none;"></div>
                                </div>
                                <select class="form-select" id="edit_student_id" name="student_id" required>
                                    <option value="">Select student</option>
                                    <?php if(!empty($students)): ?>
                                        <?php foreach($students as $student): ?>
                                            <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']); ?>
                                            <option value="<?= htmlspecialchars($student['id']); ?>"><?= htmlspecialchars($studentName); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_school_year_id" class="form-label">School Year</label>
                                <select class="form-select" id="edit_school_year_id" name="school_year_id" required>
                                    <option value="">Select school year</option>
                                    <?php if(!empty($active_sy)): ?>
                                        <?php foreach($active_sy as $sy): ?>
                                            <option value="<?= htmlspecialchars($sy['id']); ?>"><?= htmlspecialchars($sy['school_year']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="edit_height_cm" class="form-label">Height (cm)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_height_cm" name="height_cm" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="edit_weight_kg" class="form-label">Weight (kg)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_weight_kg" name="weight_kg" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="edit_bmi" class="form-label">BMI</label>
                                <input type="text" class="form-control" id="edit_bmi" name="bmi" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label d-block">Classification</label>
                                <span class="bmi-badge" id="edit_bmi_classification_badge">&mdash;</span>
                                <input type="hidden" id="edit_bmi_classification" name="bmi_classification">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_blood_type" class="form-label">Blood Type</label>
                                <select class="form-select" id="edit_blood_type" name="blood_type">
                                    <option value="">Unknown</option>
                                    <?php foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt): ?>
                                        <option value="<?= $bt ?>"><?= $bt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_vision_screening_result" class="form-label">Vision Screening Result</label>
                                <input type="text" class="form-control" id="edit_vision_screening_result" name="vision_screening_result">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_hearing_screening_result" class="form-label">Hearing Screening Result</label>
                                <input type="text" class="form-control" id="edit_hearing_screening_result" name="hearing_screening_result">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="edit_allergies" name="allergies" rows="2"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_medical_conditions" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="edit_medical_conditions" name="medical_conditions" rows="2"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_immunization_status" class="form-label">Immunization Status</label>
                                <textarea class="form-control" id="edit_immunization_status" name="immunization_status" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- view modal -->
    <div class="modal fade" id="viewHealthProfileModal" tabindex="-1" aria-labelledby="viewHealthProfileLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewHealthProfileLabel">Health Profile Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Student</label>
                            <p class="mb-0" id="view_student_name"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">School Year</label>
                            <p class="mb-0" id="view_school_year"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Blood Type</label>
                            <p class="mb-0" id="view_blood_type"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">BMI</label>
                            <p class="mb-0"><span id="view_bmi"></span> (<span id="view_bmi_classification"></span>)</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Allergies</label>
                            <p class="mb-0" id="view_allergies"></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Medical Conditions</label>
                            <p class="mb-0" id="view_medical_conditions"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Vision Screening Result</label>
                            <p class="mb-0" id="view_vision_screening_result"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hearing Screening Result</label>
                            <p class="mb-0" id="view_hearing_screening_result"></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Immunization Status</label>
                            <p class="mb-0" id="view_immunization_status"></p>
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
      <h5 class="card-header">Student Health Profiles</h5>
      <div class="table-responsive nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Student Name</th>
              <th>Height (cm)</th>
              <th>Weight (kg)</th>
              <th>BMI</th>
              <th>Classification</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
                $healthRows   = $health_records['data']         ?? [];
                $healthPage   = $health_records['current_page'] ?? 1;
                $healthPages  = $health_records['total_pages']  ?? 1;
                $healthPer    = $health_records['per_page']     ?? 10;
                $healthOffset = ($healthPage - 1) * $healthPer;
            ?>
            <?php if(!empty($healthRows)): ?>
              <?php foreach($healthRows as $index => $record): ?>
                <?php
                  $rowStudentName = trim($record['student_first_name'] . ' ' . ($record['student_middle_name'] ?? '') . ' ' . $record['student_last_name'] . ' ' . ($record['student_suffix'] ?? ''));
                  $badgeClass = 'bmi-badge-' . strtolower(str_replace(' ', '-', $record['bmi_classification'] ?? ''));
                ?>
                <tr
                  role="button"
                  style="cursor: pointer;"
                  onclick="handleHealthRowClick(
                      event,
                      '<?= htmlspecialchars($rowStudentName) ?>',
                      '<?= htmlspecialchars($record['school_year']) ?>',
                      '<?= htmlspecialchars($record['blood_type'] ?? '') ?>',
                      '<?= htmlspecialchars($record['bmi'] ?? '') ?>',
                      '<?= htmlspecialchars($record['bmi_classification'] ?? '') ?>',
                      '<?= htmlspecialchars($record['allergies'] ?? '') ?>',
                      '<?= htmlspecialchars($record['medical_conditions'] ?? '') ?>',
                      '<?= htmlspecialchars($record['vision_screening_result'] ?? '') ?>',
                      '<?= htmlspecialchars($record['hearing_screening_result'] ?? '') ?>',
                      '<?= htmlspecialchars($record['immunization_status'] ?? '') ?>'
                  )"
                >
                  <td><?= $healthOffset + $index + 1 ?></td>
                  <td><?= htmlspecialchars($rowStudentName) ?></td>
                  <td><?= htmlspecialchars($record['height_cm']) ?></td>
                  <td><?= htmlspecialchars($record['weight_kg']) ?></td>
                  <td><?= htmlspecialchars($record['bmi']) ?></td>
                  <td><span class="bmi-badge <?= $badgeClass ?>"><?= htmlspecialchars($record['bmi_classification'] ?? '') ?></span></td>
                  <td>
                    <button
                      class="btn btn-sm btn-primary"
                      data-bs-toggle="modal"
                      data-bs-target="#editHealthProfileModal"
                      onclick="event.stopPropagation(); editHealthProfile(
                          '<?= (int) $record['id'] ?>',
                          '<?= (int) $record['student_id'] ?>',
                          '<?= (int) $record['school_year_id'] ?>',
                          '<?= htmlspecialchars($record['height_cm'] ?? '') ?>',
                          '<?= htmlspecialchars($record['weight_kg'] ?? '') ?>',
                          '<?= htmlspecialchars($record['blood_type'] ?? '') ?>',
                          '<?= htmlspecialchars($record['allergies'] ?? '') ?>',
                          '<?= htmlspecialchars($record['medical_conditions'] ?? '') ?>',
                          '<?= htmlspecialchars($record['vision_screening_result'] ?? '') ?>',
                          '<?= htmlspecialchars($record['hearing_screening_result'] ?? '') ?>',
                          '<?= htmlspecialchars($record['immunization_status'] ?? '') ?>'
                      )"
                    >
                      Edit
                    </button>
                    <button
                      type="button"
                      class="btn btn-sm btn-danger d-inline"
                      onclick="event.stopPropagation(); deleteHealthProfile(<?= (int) $record['id'] ?>)"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="text-center">No student health profiles found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($healthPages > 1): ?>
      <div class="card-footer">
        <nav>
          <ul class="pagination justify-content-center mb-0">
            <li class="page-item <?php echo $healthPage <= 1 ? 'disabled' : ''; ?>">
              <a class="page-link" href="?page=<?php echo $healthPage - 1; ?>">&laquo;</a>
            </li>
            <?php for ($p = 1; $p <= $healthPages; $p++): ?>
              <li class="page-item <?php echo $p === $healthPage ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $healthPage >= $healthPages ? 'disabled' : ''; ?>">
              <a class="page-link" href="?page=<?php echo $healthPage + 1; ?>">&raquo;</a>
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
  <script src="../../../public/js/teacher/student-health.js"></script>
</body>
</html>
