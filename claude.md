Implement full CRUD for "Attendance" following the EXACT same architecture and coding 
style as my existing StudentBehavioralProfileModel.php (attached as reference). Do not 
deviate from its conventions — same property naming, same query structure, same 
error handling, same bind_param style.

REFERENCE FILE (StudentBehavioralProfileModel.php) — mirror this pattern exactly:
- extends Model, protected properties for each table name used in joins
  (e.g. protected $behavioral_profiles = 'behavioral_profiles'; protected $student = 'students'; 
  protected $sections = 'sections'; protected $school_year = 'school_year'; protected $users = 'users';)
- index($teacher_id, $student_id = null): SELECT with LEFT JOINs to students -> sections 
  -> school_year -> users, WHERE sec.adviser_id = ?, optionally AND {table}.student_id = ? 
  when $student_id is passed. Conditional bind_param depending on whether $student_id is null.
- create($data): INSERT with bind_param, wrapped in try/catch, error_log on failure, return true on success.
- update($id, $data): UPDATE ... WHERE id = ?, same bind_param/try-catch/error_log style.
- delete($id): DELETE ... WHERE id = ?, same try/catch/error_log style.
- Uses $this->con->prepare(), get_result(), fetch_all(MYSQLI_ASSOC) for reads.

ALSO read the abstract Controller class (constructor takes $model; abstract index(), 
create($data), update($id, $data), delete($id)) and the base Model class (constructor 
takes mysqli $con) — Attendance must follow the same abstraction.

DATABASE SCHEMA (from profilingdb.sql):

CREATE TABLE attendance (
  id int(11) PK AUTO_INCREMENT,
  student_id int(11) NOT NULL,          -- FK -> students.id (ON DELETE CASCADE)
  school_year_id int(11) NOT NULL,      -- FK -> school_year.id
  attendance_date date NOT NULL,
  status enum('Present','Absent','Late','Excused') NOT NULL,
  remarks text DEFAULT NULL,
  recorded_by int(11) NOT NULL,         -- FK -> users.id (teacher who recorded it)
  created_at timestamp DEFAULT current_timestamp()
);

students (id, ..., section_id FK -> sections.id, ...)
sections (id, grade_level_id, section_name, adviser_id FK -> users.id, ...)

OWNERSHIP RULE (same as behavioral_profiles): a student is only "under" a teacher when
students.section_id = sections.id AND sections.adviser_id = <teacher_id>. Every 
attendance query must be scoped through this same join/WHERE chain so a teacher can 
never read, edit, or delete another teacher's students' attendance.

FILES TO CREATE:

1. AttendanceModel.php (extends Model)
   - protected $attendance = 'attendance'; protected $student = 'students'; 
     protected $sections = 'sections'; protected $school_year = 'school_year'; 
     protected $users = 'users';
   - index($teacher_id, $student_id = null)
     SELECT a.*, s.first_name AS student_first_name, s.middle_name AS student_middle_name,
     s.last_name AS student_last_name, s.suffix AS student_suffix, 
     sy.school_year AS school_year, u.full_name AS recorded_by
     FROM attendance a
     LEFT JOIN students s ON a.student_id = s.id
     LEFT JOIN sections sec ON s.section_id = sec.id
     LEFT JOIN school_year sy ON a.school_year_id = sy.id
     LEFT JOIN users u ON a.recorded_by = u.id
     WHERE sec.adviser_id = ?
     [AND a.student_id = ? if $student_id given]
   - create($data): INSERT INTO attendance (student_id, school_year_id, attendance_date, 
     status, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?) — bind_param types "iisssi"
   - update($id, $data): UPDATE attendance SET student_id = ?, school_year_id = ?, 
     attendance_date = ?, status = ?, remarks = ?, recorded_by = ? WHERE id = ? — 
     bind_param "iisssi i" (id last)
   - delete($id): DELETE FROM attendance WHERE id = ?
   - Same try/catch + error_log("Error [action] attendance record: " . $e->getMessage()) 
     pattern as the reference file for every method.
   - Add a getById($id, $teacher_id) following the same join/WHERE pattern as index(), 
     used internally before update/delete to confirm ownership if needed — only if this 
     matches how other models in the project (if any) do ownership checks before write ops; 
     otherwise keep create/update/delete as plain as the reference file and rely on 
     the Controller/Service layer to validate student_id ownership beforehand.

2. AttendanceController.php — extends Controller, implements index(), create($data), 
   update($id, $data), delete($id) exactly matching the abstract signatures, delegating 
   to AttendanceModel (or AttendanceService if that's how other modules — e.g. behavioral 
   profiles — wire their Controller layer; check for a StudentBehavioralProfileController.php 
   or similar to confirm before assuming).

3. AttendanceService.php — only include this layer if other modules in the project 
   actually use a Service class between Controller and Model (check for e.g. 
   StudentBehavioralProfileService.php). If found, mirror its structure for validation 
   (status must be one of Present/Absent/Late/Excused, attendance_date required and valid, 
   student_id/school_year_id required integers) before calling the Model.

4. attendance.php — entry point, bootstraps mysqli connection + session, instantiates 
   Model -> Controller (-> Service if applicable), routes HTTP method to the matching 
   Controller method, reads $_GET/$_POST/php://input the same way other entry files in 
   the project do (check e.g. student_behavioral.php or similar for the exact convention).

REQUIREMENTS:
- mysqli + prepared statements only, no PDO, no raw string concatenation of values.
- Match indentation, brace style, and variable naming exactly as shown in 
  StudentBehavioralProfileModel.php.
- Every read/write on attendance must be scoped to sec.adviser_id = teacher_id.
- If you can't find a Controller/Service counterpart for behavioral_profiles or another 
  module to confirm the exact layering, ask me to upload those files before proceeding 
  instead of guessing.

Show me the full code for all files.