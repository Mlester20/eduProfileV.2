<?php
require_once __DIR__ . '/../../../app/controllers/administrative/duplicatescontroller.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/middleware/Auth.php';
AuthRole::allowOnly(['administrative']);

// Assigns a group number to each row based on the (name, birth date, school
// year) key so the view can visually separate one duplicate group from the
// next -- rows for the same group are already adjacent per the model's
// ORDER BY, so this only needs to watch for the key changing.
$groupedDuplicates = [];
$lastKey = null;
$groupNumber = 0;
foreach(($possibleDuplicates ?? []) as $record){
    $key = strtolower($record['first_name']) . '|' . strtolower($record['last_name']) . '|' . $record['birth_date'] . '|' . $record['school_year_id'];
    if($key !== $lastKey){
        $groupNumber++;
        $lastKey = $key;
    }
    $record['group_number'] = $groupNumber;
    $groupedDuplicates[] = $record;
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Possible Duplicates</title>
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

    <form action="duplicates.php" method="get" class="d-flex flex-wrap align-items-center gap-2 mb-3">
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

    <div class="card">
        <h5 class="card-header">Possible Duplicate Learners</h5>
        <p class="text-muted px-4 mb-2 small">
            Active learners that share the same full name and birth date within the same school year — likely the same
            learner entered more than once by different teachers. This is a read-only flag for manual verification;
            no records are merged or deleted automatically.
        </p>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Student Name</th>
                        <th>Birth Date</th>
                        <th>LRN</th>
                        <th>School Year</th>
                        <th>Grade & Section</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $currentGroup = null; ?>
                    <?php foreach($groupedDuplicates as $record): ?>
                        <?php
                            $studentName = trim($record['first_name'] . ' ' . ($record['middle_name'] ?? '') . ' ' . $record['last_name'] . ' ' . ($record['suffix'] ?? ''));
                            $sectionDisplay = trim(($record['grade_name'] ?? '') . ' - ' . ($record['section_name'] ?? ''));
                            $isNewGroup = $record['group_number'] !== $currentGroup;
                            $currentGroup = $record['group_number'];
                        ?>
                        <tr <?php echo $isNewGroup ? 'style="border-top: 2px solid #a1a1a1;"' : ''; ?> class="<?php echo ($record['group_number'] % 2 === 0) ? 'table-light' : ''; ?>">
                            <td><?php echo $isNewGroup ? (int) $record['group_number'] : ''; ?></td>
                            <td><?php echo htmlspecialchars($studentName); ?></td>
                            <td><?php echo htmlspecialchars($record['birth_date']); ?></td>
                            <td><?php echo htmlspecialchars($record['lrn'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($record['school_year'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($sectionDisplay); ?></td>
                            <td><?php echo htmlspecialchars($record['recorded_by_name'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($groupedDuplicates)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No possible duplicates found for the selected filter.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
</body>
</html>
