<?php
require_once __DIR__ . '/../../../app/controllers/teacher/AchievementProfileController.php';
require_once __DIR__ . '/../../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../../app/helpers/csrf.php';
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
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Achievements</title>
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

    <?php if(!$activeSchoolYear): ?>
        <div class="alert alert-warning" role="alert">
            No active school year is set. Please ask an administrator to set one before adding achievement records.
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center">
        <span class="badge bg-label-primary">
            School Year: <?php echo htmlspecialchars($activeSchoolYear['school_year'] ?? 'None set'); ?>
        </span>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAchievementModal" <?php echo $activeSchoolYear ? '' : 'disabled'; ?>>
            <i class="bx bx-plus"></i> Add Achievement
        </button>
    </div>

    <!-- add modal -->
    <div class="modal fade" id="addAchievementModal" tabindex="-1" aria-labelledby="addAchievementLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAchievementLabel">Add Achievement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/AchievementProfileController.php" method="post">
                    <?= Csrf::field() ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="student_id_search" class="form-label">Student</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="student_id_search" placeholder="Search student..." autocomplete="off">
                                    <div id="student_id_suggestions" class="list-group shadow-sm position-absolute w-100" style="top: 100%; left: 0; z-index: 5; max-height: 200px; overflow-y: auto; display: none;"></div>
                                </div>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="" selected disabled>-- Choose Student --</option>
                                    <?php foreach(($students ?? []) as $student): ?>
                                        <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'] . ' ' . ($student['suffix'] ?? '')); ?>
                                        <option value="<?php echo htmlspecialchars($student['id']); ?>"><?php echo htmlspecialchars($studentName); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">School Year</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($activeSchoolYear['school_year'] ?? 'None set'); ?>" disabled>
                                <input type="hidden" id="school_year_id" name="school_year_id" value="<?php echo htmlspecialchars($activeSchoolYear['id'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input class="form-control" type="text" name="title" id="title" placeholder="Enter achievement title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="awarding_body" class="form-label">Awarding Body</label>
                                <input class="form-control" type="text" name="awarding_body" id="awarding_body" placeholder="Enter awarding body">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" name="category" id="category" required>
                                    <option value="" selected disabled>-- Choose Category --</option>
                                    <option value="Academic">Academic</option>
                                    <option value="Sports">Sports</option>
                                    <option value="Leadership">Leadership</option>
                                    <option value="Arts & Culture">Arts & Culture</option>
                                    <option value="Co-Curricular">Co-Curricular</option>
                                    <option value="Community Service">Community Service</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="level" class="form-label">Level</label>
                                <select class="form-select" name="level" id="level" required>
                                    <option value="" selected disabled>-- Choose Level --</option>
                                    <option value="School">School</option>
                                    <option value="District">District</option>
                                    <option value="Division">Division</option>
                                    <option value="Regional">Regional</option>
                                    <option value="National">National</option>
                                    <option value="International">International</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="date_received" class="form-label">Date Received</label>
                                <input class="form-control" type="date" name="date_received" id="date_received" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="2" placeholder="Enter description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_achievement">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- edit modal -->
    <div class="modal fade" id="editAchievementModal" tabindex="-1" aria-labelledby="editAchievementLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAchievementLabel">Edit Achievement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/teacher/AchievementProfileController.php" method="post">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" id="edit_achievement_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_student_id_search" class="form-label">Student</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="edit_student_id_search" placeholder="Search student..." autocomplete="off">
                                    <div id="edit_student_id_suggestions" class="list-group shadow-sm position-absolute w-100" style="top: 100%; left: 0; z-index: 5; max-height: 200px; overflow-y: auto; display: none;"></div>
                                </div>
                                <select class="form-select" name="student_id" id="edit_student_id" required>
                                    <option value="" selected disabled>-- Choose Student --</option>
                                    <?php foreach(($students ?? []) as $student): ?>
                                        <?php $studentName = trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'] . ' ' . ($student['suffix'] ?? '')); ?>
                                        <option value="<?php echo htmlspecialchars($student['id']); ?>"><?php echo htmlspecialchars($studentName); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">School Year</label>
                                <input type="text" class="form-control" id="edit_school_year_display" disabled>
                                <input type="hidden" name="school_year_id" id="edit_school_year_id">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_title" class="form-label">Title</label>
                                <input class="form-control" type="text" name="title" id="edit_title" placeholder="Enter achievement title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_awarding_body" class="form-label">Awarding Body</label>
                                <input class="form-control" type="text" name="awarding_body" id="edit_awarding_body" placeholder="Enter awarding body">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_category" class="form-label">Category</label>
                                <select class="form-select" name="category" id="edit_category" required>
                                    <option value="" selected disabled>-- Choose Category --</option>
                                    <option value="Academic">Academic</option>
                                    <option value="Sports">Sports</option>
                                    <option value="Leadership">Leadership</option>
                                    <option value="Arts & Culture">Arts & Culture</option>
                                    <option value="Co-Curricular">Co-Curricular</option>
                                    <option value="Community Service">Community Service</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_level" class="form-label">Level</label>
                                <select class="form-select" name="level" id="edit_level" required>
                                    <option value="" selected disabled>-- Choose Level --</option>
                                    <option value="School">School</option>
                                    <option value="District">District</option>
                                    <option value="Division">Division</option>
                                    <option value="Regional">Regional</option>
                                    <option value="National">National</option>
                                    <option value="International">International</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_date_received" class="form-label">Date Received</label>
                                <input class="form-control" type="date" name="date_received" id="edit_date_received" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="edit_description" rows="2" placeholder="Enter description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_achievement">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">Achievement Profiles</h5>
        <div class="table-responsive nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>School Year</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Level</th>
                        <th>Date Received</th>
                        <th>Awarding Body</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $achievementRows   = $achievementProfiles['data']         ?? [];
                        $achievementPage   = $achievementProfiles['current_page'] ?? 1;
                        $achievementPages  = $achievementProfiles['total_pages']  ?? 1;
                        $achievementPer    = $achievementProfiles['per_page']     ?? 10;
                        $achievementOffset = ($achievementPage - 1) * $achievementPer;
                    ?>
                    <?php foreach($achievementRows as $index => $profile): ?>
                        <tr>
                            <td><?php echo $achievementOffset + $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($profile['student_first_name'] . ' ' . $profile['student_middle_name'] . ' ' . $profile['student_last_name'] . ' ' . $profile['student_suffix']); ?></td>
                            <td><?php echo htmlspecialchars($profile['school_year']); ?></td>
                            <td><?php echo htmlspecialchars($profile['title']); ?></td>
                            <td><?php echo htmlspecialchars($profile['category']); ?></td>
                            <td><?php echo htmlspecialchars($profile['level']); ?></td>
                            <td><?php echo htmlspecialchars($profile['date_received']); ?></td>
                            <td><?php echo htmlspecialchars($profile['awarding_body']); ?></td>
                            <td>
                                <button
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editAchievementModal"
                                    onclick="editAchievementProfile(
                                        '<?php echo htmlspecialchars($profile['id']); ?>',
                                        '<?php echo htmlspecialchars($profile['student_id']); ?>',
                                        '<?php echo htmlspecialchars($profile['school_year_id']); ?>',
                                        '<?php echo htmlspecialchars($profile['school_year']); ?>',
                                        '<?php echo htmlspecialchars($profile['title']); ?>',
                                        '<?php echo htmlspecialchars($profile['category']); ?>',
                                        '<?php echo htmlspecialchars($profile['level']); ?>',
                                        '<?php echo htmlspecialchars($profile['date_received']); ?>',
                                        '<?php echo htmlspecialchars($profile['awarding_body']); ?>',
                                        '<?php echo htmlspecialchars($profile['description']); ?>'
                                    )"
                                >
                                    <i class="bx bx-edit"></i>
                                </button>

                                <form action="../../../app/controllers/teacher/AchievementProfileController.php" method="post" class="d-inline">
                                    <?= Csrf::field() ?>

                                    <input type="hidden" name="id" value="<?php echo $profile['id']; ?>">
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        name="delete_achievement"
                                        onclick="return confirm('Are you sure you want to delete this achievement profile?');">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($achievementPages > 1): ?>
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item <?php echo $achievementPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $achievementPage - 1; ?>">&laquo;</a>
              </li>
              <?php for ($p = 1; $p <= $achievementPages; $p++): ?>
                <li class="page-item <?php echo $p === $achievementPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $achievementPage >= $achievementPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $achievementPage + 1; ?>">&raquo;</a>
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
  <script src="../../../public/js/teacher/achievement-profile.js"></script>
</body>
</html>