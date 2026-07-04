# Task: New StudentService for Teacher-Scoped, Searchable Student Dropdown

## Context

MVC PHP app (mysqli, no framework, Sneat Bootstrap admin template). Files to read before touching anything:

- `app/models/teacher/StudentsModel.php`
- `app/models/Model.php` — base class, just holds `$con`
- `app/controllers/teacher/StudentBehaviorProfileController.php`
- `app/models/teacher/StudentBehavioralProfileModel.php`
- `resources/views/teacher/student-behavior.php`
- `public/js/teacher/student-behavioral.js`
- `app/models/admin/SchoolYearModel.php`

## Confirmed schema (verified against `profilingdb.sql`)

```sql
CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `lrn` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `address` varchar(50) DEFAULT NULL,
  `school_year_id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `recorded_by` int(11) NOT NULL
);

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `adviser_id` int(11) DEFAULT NULL   -- FK to users.id, the teacher
);

CREATE TABLE `grade_levels` (
  `id` int(11) NOT NULL,
  `grade_name` varchar(50) NOT NULL
);
```

There is **no `academic_history` table in this project** — ignore any earlier reference to it. The relationship is direct and one hop:

**Confirmed business rule:** when a teacher creates a student record, the section is auto-assigned to that teacher's own advisory section (the "Grade Level & Section" field is shown locked/read-only in the student-creation form, pre-filled with the teacher's section — the teacher never picks a different section). So `students.section_id` is always already scoped to the section the recording teacher advises. This means filtering by `sections.adviser_id = <teacher>` is sufficient and correct — no need to also check `recorded_by`.

## Goal

Create a **new service class** (do not modify `StudentsModel.php`'s existing methods) that returns only students belonging to the logged-in teacher's advisory section(s), and make the student dropdown in the behavior-profile create/edit modals searchable instead of a long plain scroll list.

## Requirements

### 1. New `app/services/StudentService.php`
- `class StudentService extends Model` — same lightweight pattern as any model in this codebase (constructor just needs `$con`, inherited from `Model`). Create `app/services/` if it doesn't exist yet and tell me.
- Method: `getStudentsByAdviser(int $teacherId, ?int $schoolYearId = null): array`
  - Join `students` to `sections` on `students.section_id = sections.id`, filter `sections.adviser_id = ?`.
  - If `$schoolYearId` is passed, additionally filter `students.school_year_id = ?`.
  - Also join `grade_levels` (on `sections.grade_level_id` or `students.grade_level_id` — check which one is authoritative, they should agree since section is auto-assigned; flag it if they ever don't) so returned rows include `grade_name` and `section_name`, matching the same field shape `StudentsModel::index()` already returns — the view shouldn't need to change how it reads `$student['...']`.
  - Follow `StudentsModel.php`'s exact conventions: prepared statements, `bind_param`, try/catch with `error_log()`, return `[]` on failure (not `null`/`false`).
- Keep this service minimal — one filtered query method, nothing else.

### 2. Controller — `StudentBehaviorProfileController.php`
- Add `$this->studentService = new StudentService($con);` in the constructor, alongside the existing `$this->students = new StudentsModel($con)`.
- Change `getStudents()` to call `$this->studentService->getStudentsByAdviser($_SESSION['id'], $activeSchoolYearId)` instead of `$this->students->index()`.
- Check `SchoolYearModel::getActiveSy()`'s actual return shape (single associative row vs. array of rows) before extracting the school year id — don't assume.
- If the teacher has no section where they're `adviser_id`, return an empty array — the view's existing `empty($students)` check already handles that.

### 3. View — `resources/views/teacher/student-behavior.php`
- Both `#student_id` (create modal) and `#edit_student_id` (edit modal) selects currently loop `$students` as plain `<option>`s. Keep the underlying `<select>` intact (needed for form submission and for `editStudentBehavioral()` to set its value), but make it searchable.
- Check `public/assets/vendor/libs/` first for an already-bundled searchable-select library (Sneat templates often ship `select2` or `tom-select`). Use it if present. Only build a custom vanilla-JS search-filter (a text input that filters visible `<option>`s live) if nothing is already bundled — don't add a new CDN/npm dependency the rest of the project doesn't already use.
- Apply the same treatment to both modals.

### 4. JS — `public/js/teacher/student-behavioral.js`
- If a searchable-select library wraps the native `<select>`, verify `editStudentBehavioral()`'s `document.getElementById('edit_student_id').value = student_id` still visually updates the widget's displayed selection when the edit modal opens — some libraries need their own "set selected" call in addition to the native `.value` assignment, or the visible dropdown will show the wrong student even though the underlying `<select>` value is technically correct.

## Constraints

- Do not modify existing methods in `StudentsModel.php` — additive only, via the new service.
- Do not modify `behavioral_profiles` table or `StudentBehavioralProfileModel.php`.
- Do not reintroduce or reference `academic_history` — it doesn't exist in this project.
- Match existing conventions: mysqli prepared statements, try/catch + `error_log()`, `$_SESSION`-based auth, `FlashMessage` helper, Bootstrap modal structure already in the view.
- After implementing, show me a diff summary of every file changed/created, and explicitly confirm: (a) what `getActiveSy()` actually returns, (b) whether `sections.grade_level_id` and `students.grade_level_id` ever disagree in real data, (c) whether a searchable-select library was already bundled or you built a custom one, (d) any teacher in `users` with no matching `sections.adviser_id` yet (so I know which teachers still need a section assignment before this feature is useful to them).