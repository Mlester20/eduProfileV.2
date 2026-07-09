Implement full CRUD for "Student Health Profile" following the EXACT same MVC architecture, 
naming conventions, and coding style already established in this project (mirror 
StudentBehavioralProfileModel.php and the Attendance module for reference — read those 
files first before writing any code).

FILES TO CREATE:
1. StudentHealthModel.php
2. StudentHealthController.php
3. StudentHealthService.php  (new — create this even though older modules like 
   StudentBehavioralProfileModel didn't have one, since BMI calculation validation 
   and business rules belong in a Service layer, not the Model or Controller)
4. student-health.php
5. student-health.js

CONTEXT — read before coding:
1. Read the abstract `Controller` class (constructor takes $model; abstract index(), 
   create($data), update($id, $data), delete($id)).
2. Read the base `Model` class (constructor takes mysqli $con).
3. Read StudentBehavioralProfileModel.php for the exact Model pattern to copy: 
   property-per-table naming (protected $health_profiles = 'health_profiles'; etc.), 
   LEFT JOIN chain through students -> sections -> WHERE sec.adviser_id = ? for 
   teacher-scoped ownership, try/catch + error_log() per method, mysqli prepared 
   statements with bind_param.
4. Read the Attendance module (AttendanceModel.php / AttendanceController.php / 
   attendance.php) for how a Service layer + entry-point routing was structured most 
   recently in this project — follow that same shape for StudentHealthService.php 
   and student-health.php.

DATABASE TABLE (already designed, create this if it doesn't exist yet in your DB):

CREATE TABLE `health_profiles` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL,
  `bmi_classification` enum('Severely Wasted','Wasted','Normal','Overweight','Obese') DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `vision_screening_result` varchar(100) DEFAULT NULL,
  `hearing_screening_result` varchar(100) DEFAULT NULL,
  `immunization_status` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

FK: student_id -> students(id) ON DELETE CASCADE, school_year_id -> school_year(id), 
recorded_by -> users(id). Same pattern as behavioral_profiles/developmental_profiles.

OWNERSHIP RULE: same as every other module — a student is only under a teacher when 
students.section_id = sections.id AND sections.adviser_id = <teacher_id>. All 
queries must be scoped through this join.

BMI AUTO-CALCULATION (student-health.js) — CLIENT-SIDE ONLY:
- The form has height_cm and weight_kg input fields. BMI and BMI classification are 
  NOT manually entered — they must be READ-ONLY fields that auto-compute and update 
  live via JavaScript as the teacher types.
- Formula: BMI = weight_kg / ((height_cm / 100) ** 2), rounded to 2 decimal places.
- Recalculate on every 'input' event on both height_cm and weight_kg fields. If either 
  field is empty or 0, leave BMI blank instead of showing NaN/Infinity.
- Classification thresholds (standard adult BMI-for-age reference used by this system's 
  enum — Severely Wasted / Wasted / Normal / Overweight / Obese):
    < 16.0            -> Severely Wasted
    16.0 - 18.4        -> Wasted
    18.5 - 24.9         -> Normal
    25.0 - 29.9          -> Overweight
    >= 30.0                -> Obese
  NOTE: these are simplified adult BMI cutoffs. Real DepEd Nutritional Status uses 
  BMI-for-age percentile charts (different per age/sex). Flag this in your code comments 
  so it's easy to swap in an age/sex-based lookup table later if needed — for now, use 
  the simplified thresholds above since that's what the enum values map to.
- Update the bmi_classification text field/badge live alongside the BMI number, with 
  the same color-coded style already used elsewhere in this project (e.g. Attendance 
  status colors: red/yellow/green/blue-style semantics — pick similarly intuitive 
  colors here, e.g. red for Severely Wasted/Obese extremes, green for Normal).
- On form submit, send the JS-calculated bmi and bmi_classification values along with 
  height_cm/weight_kg to the backend — but the backend must NOT blindly trust these; 
  recompute and validate server-side too (see Service layer below) so a tampered 
  client request can't insert a fake BMI/classification pair.

BACKEND:

1. StudentHealthModel.php
   - protected $health_profiles = 'health_profiles'; protected $student = 'students'; 
     protected $sections = 'sections'; protected $school_year = 'school_year';
   - index($teacher_id, $student_id = null): SELECT hp.*, s.first_name AS 
     student_first_name, s.middle_name AS student_middle_name, s.last_name AS 
     student_last_name, s.suffix AS student_suffix, sy.school_year AS school_year 
     FROM health_profiles hp LEFT JOIN students s ON hp.student_id = s.id 
     LEFT JOIN sections sec ON s.section_id = sec.id LEFT JOIN school_year sy ON 
     hp.school_year_id = sy.id WHERE sec.adviser_id = ? [AND hp.student_id = ? if given]
   - getById($id, $teacher_id): same join pattern, WHERE hp.id = ? AND sec.adviser_id = ?
   - create($data): INSERT INTO health_profiles (student_id, school_year_id, height_cm, 
     weight_kg, bmi, bmi_classification, blood_type, allergies, medical_conditions, 
     vision_screening_result, hearing_screening_result, immunization_status, recorded_by) 
     VALUES (...) with matching bind_param types
   - update($id, $data): UPDATE health_profiles SET ... WHERE id = ?
   - delete($id): DELETE FROM health_profiles WHERE id = ?
   - Same try/catch + error_log("Error [action] health profile: " . $e->getMessage()) 
     pattern as StudentBehavioralProfileModel.php for every method.

2. StudentHealthService.php (NEW)
   - Sits between Controller and Model. Responsibilities:
     - Validate student_id, school_year_id required and are integers.
     - Verify student_id belongs to the logged-in teacher (via the same 
       students->sections->adviser_id ownership check) before allowing create/update — 
       reuse/query the Model or a shared helper for this, do not skip it.
     - SERVER-SIDE RECOMPUTE of BMI: given height_cm and weight_kg from the request, 
       recalculate bmi and bmi_classification using the same formula/thresholds as the 
       JS, and OVERWRITE whatever bmi/bmi_classification values the client sent — never 
       trust client-calculated BMI directly.
     - Validate height_cm and weight_kg are positive numbers within a sane range 
       (e.g. height 50-250 cm, weight 5-200 kg) — reject with a clear error otherwise.
     - Validate blood_type against a known set if provided (A+, A-, B+, B-, AB+, AB-, O+, O-) 
       or allow null.
     - Return consistent success/error arrays to the Controller.

3. StudentHealthController.php
   - extends Controller, implements index(), create($data), update($id, $data), 
     delete($id) matching the abstract signatures.
   - Delegates all logic to StudentHealthService (not directly to the Model).
   - Pulls teacher_id from session and passes it down.

4. student-health.php
   - Entry point: bootstraps mysqli connection + session, instantiates 
     Model -> Service -> Controller, routes HTTP method (GET=index, POST=create, 
     PUT/PATCH=update, DELETE=delete) to the Controller, matching how attendance.php 
     does its routing/input parsing (php://input for JSON, $_GET for query params).
   - Renders the list/table of health records for the teacher's students plus a 
     form (with the readonly BMI/classification fields wired to student-health.js).

5. student-health.js
   - Handles the live BMI calculation described above.
   - Handles form submit via fetch/AJAX to student-health.php, following the same 
     request/response conventions already used by attendance's JS (check its fetch 
     calls, payload shape, and success/error handling to match style).
   - Handles populating the form for edit mode (fetch existing record, recompute BMI 
     display from stored height/weight on load).

REQUIREMENTS:
- mysqli + prepared statements only, no raw string concatenation of values.
- BMI/classification are always server-recomputed in the Service layer regardless of 
  what the client sends — this is non-negotiable, treat client BMI as display-only.
- Every query scoped to sec.adviser_id = teacher_id, same as every other module.
- Match existing naming, indentation, and error-handling conventions from 
  StudentBehavioralProfileModel.php and the Attendance module.
- If you're unsure how the Attendance module wired its Service/Controller/entry-point 
  layering, ask me to confirm before guessing.

Show me the full code for all five files.