<?php
require_once __DIR__ . '/../../../database/config/config.php';
require_once __DIR__ . '/../../../app/controllers/administrative/learnerprofilecontroller.php';
require_once __DIR__ . '/../../../app/helpers/SchoolSettings.php';
require_once __DIR__ . '/../../../app/helpers/StudentsAge.php';
require_once __DIR__ . '/../../../app/services/AddressService.php';
require_once __DIR__ . '/../../../app/models/admin/SectionsModel.php';

$school = SchoolSettings::get($con);

if($profile === null){
    header('Location: learner-profile.php');
    exit();
}

$info = $profile['info'];
$pg = $profile['parent_guardian'];
$h = $profile['health'];
$fullName = trim($info['first_name'] . ' ' . ($info['middle_name'] ?? '') . ' ' . $info['last_name'] . ' ' . ($info['suffix'] ?? ''));

// "Prepared by" -- the section's current adviser, not recorded_by (who may
// not be the student's present teacher). Blank line if the section has no
// adviser assigned yet.
$adviserName = !empty($info['section_id'])
    ? (new SectionsModel($con))->getAdviserNameBySectionId($info['section_id'])
    : null;

function printValue($value){
    $value = trim((string) ($value ?? ''));
    return $value !== '' ? htmlspecialchars($value) : '<span class="pv-empty">&mdash;</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?php require_once __DIR__ . '/../../../app/helpers/title.php'; ?> | Print Learner's Profile</title>
  <link rel="icon" type="image/x-icon" href="../../../public/assets/img/favicon/logo.png" />
  <style>
    @page { size: A4; margin: 14mm 16mm; }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      padding: 24px;
      background: #fff;
      color: #1a1a1a;
      font-family: "Segoe UI", Arial, sans-serif;
      font-size: 11.5px;
      line-height: 1.4;
    }

    .no-print { }
    @media print {
      .no-print { display: none !important; }
      body { padding: 0; }
    }

    /* ---- Toolbar (screen only) ---- */
    .print-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid #ddd;
    }
    .print-toolbar a, .print-toolbar button {
      font-family: inherit;
      font-size: 13px;
      padding: 7px 14px;
      border-radius: 6px;
      border: 1px solid #8B5E3C;
      background: #fff;
      color: #8B5E3C;
      cursor: pointer;
      text-decoration: none;
    }
    .print-toolbar button.primary {
      background: #8B5E3C;
      color: #fff;
    }

    /* ---- Letterhead ---- */
    .lp-letterhead {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      border-bottom: 2px solid #1a1a1a;
      padding-bottom: 8px;
      margin-bottom: 10px;
    }
    .lp-letterhead img { width: 58px; height: 58px; flex-shrink: 0; }
    .lp-letterhead .lp-gov-text { flex: 1; text-align: center; }
    .lp-letterhead .lp-gov-text p { margin: 0; }
    .lp-letterhead .lp-republic { font-size: 11px; }
    .lp-letterhead .lp-deped { font-size: 12px; font-weight: 700; letter-spacing: .5px; }
    .lp-letterhead .lp-region { font-size: 10.5px; }
    .lp-letterhead .lp-school { font-size: 12px; font-weight: 700; margin-top: 2px; }
    .lp-letterhead .lp-spacer { width: 58px; flex-shrink: 0; }

    .lp-title {
      text-align: center;
      margin: 10px 0 14px;
    }
    .lp-title h1 {
      font-size: 16px;
      letter-spacing: 1px;
      margin: 0;
      text-transform: uppercase;
    }
    .lp-title p { margin: 2px 0 0; font-size: 11px; color: #333; }

    /* ---- Identity block: 2x2 photo + core facts ---- */
    .lp-identity {
      display: flex;
      gap: 14px;
      margin-bottom: 14px;
      break-inside: avoid;
    }
    .lp-photo-box {
      width: 2in;
      height: 2in;
      border: 1px solid #1a1a1a;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: #f7f7f7;
    }
    .lp-photo-box img { width: 100%; height: 100%; object-fit: cover; }
    .lp-identity-facts {
      flex: 1;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px 16px;
      align-content: start;
    }
    .lp-identity-facts .full-row { grid-column: 1 / -1; }
    .lp-fact-label {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .4px;
      color: #555;
      display: block;
    }
    .lp-fact-value {
      font-size: 12.5px;
      font-weight: 600;
    }

    /* ---- Bordered form sections ---- */
    .lp-section {
      margin-bottom: 10px;
      break-inside: avoid;
    }
    .lp-section-title {
      background: #e9e9e9;
      border: 1px solid #1a1a1a;
      border-bottom: none;
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
      padding: 4px 8px;
      break-after: avoid;
    }
    table.lp-form-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
    }
    table.lp-form-table td {
      border: 1px solid #1a1a1a;
      padding: 4px 8px;
      vertical-align: top;
      font-size: 11px;
      word-wrap: break-word;
    }
    table.lp-form-table td.lp-label {
      background: #f2f2f2;
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .3px;
      color: #333;
      width: 16%;
      white-space: nowrap;
    }
    .pv-empty { color: #999; }

    /* Parent/Guardian table: one row per field, one column per person */
    table.lp-pg-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.lp-pg-table th, table.lp-pg-table td {
      border: 1px solid #1a1a1a;
      padding: 4px 8px;
      font-size: 11px;
      vertical-align: top;
      word-wrap: break-word;
    }
    table.lp-pg-table th {
      background: #e9e9e9;
      font-size: 10px;
      text-transform: uppercase;
      text-align: left;
    }
    table.lp-pg-table td.lp-label {
      background: #f2f2f2;
      font-size: 9px;
      text-transform: uppercase;
      color: #333;
      width: 16%;
      white-space: nowrap;
    }

    /* ---- Signature block ---- */
    .lp-signatures {
      display: flex;
      justify-content: space-between;
      gap: 40px;
      margin-top: 28px;
      break-inside: avoid;
    }
    .lp-signature { flex: 1; text-align: center; }
    .lp-signature .sig-line {
      border-top: 1px solid #1a1a1a;
      margin: 36px 10px 4px;
    }
    .lp-signature .sig-name { font-weight: 700; font-size: 11.5px; }
    .lp-signature .sig-role { font-size: 9.5px; color: #555; }
    .lp-signature .sig-caption { font-size: 9px; color: #555; margin-top: 10px; }

    /* ---- Footer ---- */
    .lp-footer {
      margin-top: 18px;
      padding-top: 6px;
      border-top: 1px solid #ccc;
      display: flex;
      justify-content: space-between;
      font-size: 9px;
      color: #555;
    }
  </style>
</head>
<body>

    <div class="print-toolbar no-print">
        <a href="learner-profile.php?student_id=<?php echo (int) $info['id']; ?>">&laquo; Back to Profile</a>
        <button type="button" class="primary" onclick="window.print()">Print</button>
    </div>

    <div class="lp-letterhead">
        <img src="../../../public/assets/img/favicon/logo.png" alt="School Logo">
        <div class="lp-gov-text">
            <p class="lp-republic">Republic of the Philippines</p>
            <p class="lp-deped">DEPARTMENT OF EDUCATION</p>
            <p class="lp-region"><?php echo htmlspecialchars($school['region']); ?> &nbsp;&bull;&nbsp; <?php echo htmlspecialchars($school['division']); ?> &nbsp;&bull;&nbsp; <?php echo htmlspecialchars($school['district']); ?></p>
            <p class="lp-school"><?php echo htmlspecialchars($school['school_name']); ?> (School ID: <?php echo htmlspecialchars($school['school_id']); ?>)</p>
        </div>
        <div class="lp-spacer"></div>
    </div>

    <div class="lp-title">
        <h1>Learner's Profile</h1>
        <p>School Year <?php echo htmlspecialchars($info['school_year'] ?? ''); ?></p>
    </div>

    <?php
        $learnerAvatarSrc = $info['picture'] ?? null;
        $photoFullSrc = null;
        if($learnerAvatarSrc){
            $relPath = (strpos($learnerAvatarSrc, '/') !== false) ? $learnerAvatarSrc : ('storage/students/' . $learnerAvatarSrc);
            $fullPath = __DIR__ . '/../../../' . $relPath;
            if(file_exists($fullPath)) $photoFullSrc = '../../../' . $relPath;
        }
    ?>
    <div class="lp-identity">
        <div class="lp-photo-box">
            <?php if($photoFullSrc): ?>
                <img src="<?php echo htmlspecialchars($photoFullSrc); ?>" alt="2x2 photo">
            <?php endif; ?>
        </div>
        <div class="lp-identity-facts">
            <div class="full-row">
                <span class="lp-fact-label">Full Name</span>
                <span class="lp-fact-value"><?php echo printValue($fullName); ?></span>
            </div>
            <div>
                <span class="lp-fact-label">LRN</span>
                <span class="lp-fact-value"><?php echo printValue($info['lrn'] ?? ''); ?></span>
            </div>
            <div>
                <span class="lp-fact-label">Grade & Section</span>
                <span class="lp-fact-value"><?php echo printValue(($info['grade_name'] ?? '') . ' - ' . ($info['section_name'] ?? '')); ?></span>
            </div>
            <div>
                <span class="lp-fact-label">Sex</span>
                <span class="lp-fact-value"><?php echo printValue($info['gender'] ?? ''); ?></span>
            </div>
            <div>
                <span class="lp-fact-label">Birthdate</span>
                <span class="lp-fact-value"><?php echo printValue(!empty($info['birth_date']) ? (new DateTime($info['birth_date']))->format('F j, Y') : ''); ?></span>
            </div>
            <div>
                <span class="lp-fact-label">Age</span>
                <span class="lp-fact-value"><?php echo printValue(StudentsAge::calculateAge($info['birth_date'])); ?></span>
            </div>
            <div>
                <span class="lp-fact-label">Status</span>
                <span class="lp-fact-value"><?php echo printValue(ucfirst($info['status'] ?? '')); ?></span>
            </div>
        </div>
    </div>

    <div class="lp-section">
        <div class="lp-section-title">Learner Information</div>
        <table class="lp-form-table">
            <tr>
                <td class="lp-label">Age (as of June)</td>
                <td><?php echo printValue($info['age_as_of_june'] ?? ''); ?></td>
                <td class="lp-label">Mother Tongue</td>
                <td><?php echo printValue($info['mother_tongue'] ?? ''); ?></td>
            </tr>
            <tr>
                <td class="lp-label">Address</td>
                <td colspan="3"><?php echo printValue(AddressService::formatFullAddress($info)); ?></td>
            </tr>
        </table>
    </div>

    <div class="lp-section">
        <div class="lp-section-title">Background Information</div>
        <table class="lp-form-table">
            <tr>
                <td class="lp-label">IP / Ethnic Group</td>
                <td><?php echo printValue($info['ip_ethnic_group'] ?? ''); ?></td>
                <td class="lp-label">Religion</td>
                <td><?php echo printValue($info['religion'] ?? ''); ?></td>
            </tr>
            <tr>
                <td class="lp-label">Learning Modality</td>
                <td colspan="3"><?php echo printValue($info['learning_modality'] ?? ''); ?></td>
            </tr>
        </table>
    </div>

    <div class="lp-section">
        <div class="lp-section-title">Parent / Guardian Information</div>
        <?php
            $pgPersons = [
                ['label' => 'Father', 'prefix' => 'father_', 'nameKey' => 'father_name', 'ageKey' => 'fathers_age', 'roleLabel' => 'Occupation', 'roleKey' => 'father_occupation', 'contactKey' => 'father_contact'],
                ['label' => 'Mother', 'prefix' => 'mother_', 'nameKey' => 'mother_name', 'ageKey' => 'mothers_age', 'roleLabel' => 'Occupation', 'roleKey' => 'mother_occupation', 'contactKey' => 'mother_contact'],
                ['label' => 'Guardian', 'prefix' => 'guardian_', 'nameKey' => 'guardian_name', 'ageKey' => 'guardians_age', 'roleLabel' => 'Relationship', 'roleKey' => 'guardian_relationship', 'contactKey' => 'guardian_contact'],
            ];
        ?>
        <table class="lp-pg-table">
            <tr>
                <th style="width: 16%;"></th>
                <?php foreach($pgPersons as $person): ?>
                    <th><?php echo htmlspecialchars($person['label']); ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td class="lp-label">Name</td>
                <?php foreach($pgPersons as $person): ?>
                    <td><?php echo printValue($pg[$person['nameKey']] ?? ''); ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td class="lp-label">Age</td>
                <?php foreach($pgPersons as $person): ?>
                    <td><?php echo printValue($pg[$person['ageKey']] ?? ''); ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td class="lp-label">Occ. / Relationship</td>
                <?php foreach($pgPersons as $person): ?>
                    <td><?php echo printValue($pg[$person['roleKey']] ?? ''); ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td class="lp-label">Contact</td>
                <?php foreach($pgPersons as $person): ?>
                    <td><?php echo printValue($pg[$person['contactKey']] ?? ''); ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td class="lp-label">Address</td>
                <?php foreach($pgPersons as $person): ?>
                    <td><?php echo printValue(AddressService::formatPersonAddress($pg, $person['prefix'])); ?></td>
                <?php endforeach; ?>
            </tr>
        </table>
    </div>

    <div class="lp-section">
        <div class="lp-section-title">Remarks</div>
        <table class="lp-form-table">
            <tr>
                <td style="width: 100%;"><?php echo printValue($info['remarks'] ?? ''); ?></td>
            </tr>
        </table>
    </div>

    <div class="lp-signatures">
        <div class="lp-signature">
            <div class="sig-line"></div>
            <div class="sig-name"><?php echo $adviserName ? htmlspecialchars($adviserName) : '&nbsp;'; ?></div>
            <div class="sig-role">Adviser</div>
            <div class="sig-caption">Prepared by (Signature over Printed Name)</div>
        </div>
        <div class="lp-signature">
            <div class="sig-line"></div>
            <div class="sig-name"><?php echo !empty($school['school_head']) ? htmlspecialchars($school['school_head']) : '&nbsp;'; ?></div>
            <div class="sig-role">School Principal</div>
            <div class="sig-caption">Noted by (Signature over Printed Name)</div>
        </div>
    </div>

    <div class="lp-footer">
        <span>Date printed: <?php echo date('F j, Y g:i A'); ?></span>
        <span>System-generated from EduProfile</span>
    </div>

</body>
</html>
