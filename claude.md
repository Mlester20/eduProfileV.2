# Task: Administrative "Compiled Records" Module + `students.status` Archive Column

## IMPORTANT correction from earlier discussion
The actual role string stored in `users.role` for this role is
**`administrative`** (confirmed from `profilingdb.sql` — see the `users`
table seed data: `id 3, full_name 'Administrative', role 'administrative'`,
and `audit_logs` entries like `role: 'administrative'`). Any earlier prompt
in this thread that said `administrative_assistant` was a placeholder guess
— use `AuthRole::allowOnly(['administrative'])`, not
`administrative_assistant`, when guarding views/controllers for this role.

## MANDATORY — read the actual codebase first, don't assume structure
Before writing anything:
1. Read the full current schema in `profilingdb.sql` — pay special
   attention to `section_teacher_assignments` (section_id, teacher_id,
   school_year_id), `sections` (has its own `adviser_id`), `school_year`
   (has a `status` enum: active/inactive/archived), `students`, and every
   `*_profiles` table (`academic_profiles`, `achievements_profiles`,
   `behavioral_profiles`, `developmental_profiles`, `health_profiles`) plus
   `attendance`.
2. **Find the existing "rollover to new school year" feature.** It already
   exists — `audit_logs` shows a real action: `'Rolling over students to
   new school year'` under module `'Students'`, performed by the
   `administrative` role. Locate the controller/model that implements this
   (search for something like a Students/Rollover controller/model under
   the administrative area) and read it fully — do not write a new rollover
   flow from scratch, extend the existing one.
3. Read `AcademicProfileModel.php` / `AcademicProfileController.php` /
   `academic.php` (teacher module) again for the base MVC conventions
   (bind_param style, try/catch + error_log, FlashMessage + redirect,
   Bootstrap modal markup) — the new administrative module should still
   follow this same coding style even though its query logic differs.
4. Check `app/middleware/Auth.php` for how `AuthRole::allowOnly()` role
   strings are validated, and confirm `administrative` is already a
   recognized role there (it appears to be, based on audit_logs usage).

---

## Part 1 — Add `students.status` (archive column)

```sql
ALTER TABLE `students`
  ADD COLUMN `status` enum('active','archived') NOT NULL DEFAULT 'active'
  AFTER `recorded_by`;
```

Reasoning: there is intentionally no `enrollment_history` table in this
system (out of scope — no registrar-driven enrollment tracking). Instead,
based on the existing rollover behavior already observed in the data (a
student who moves to a new school year gets a **new row** in `students`
with a new `school_year_id`, keeping the old row intact for historical
records — see `students` id 10 (SY 8, original) vs id 11 (SY 14, rolled-over
copy) in the seed data), the **old row** should now be marked
`status = 'archived'` once it has been rolled over, since it no longer
represents the student's current standing — it just stays in the DB so its
`academic_profiles` / `attendance` / `behavioral_profiles` / etc. (which are
still foreign-keyed to that old `student_id`) remain intact for compiled/
historical reporting.

### 1a. Update the existing rollover logic
In the rollover method you located in step 2 above: after successfully
inserting the new student row for the new school year, **update the
original student row's `status` to `'archived'`** in the same
transaction/flow (don't leave this as a separate manual step). Keep the
newly-inserted row's `status` as `'active'` (the column default already
handles this).

### 1b. Filter archived students out of teacher-facing views
Everywhere a teacher currently lists/selects students (e.g.
`AcademicProfileModel::index()`, `StudentsModel` listing, any student
dropdown used for Add modals across the teacher module), add
`AND s.status = 'active'` (or `students.status = 'active'`, matching
whatever alias is used in that query) to the WHERE clause, so archived/
rolled-over students stop appearing in active-year workflows. Go through
each teacher model that queries `students` and apply this consistently —
list out which files you changed.

---

## Part 2 — Administrative "Compiled Records" module

### Access model — how this differs from the teacher module
A teacher only sees their own advisees' data (`sections.adviser_id =
teacher_id`, or via `section_teacher_assignments` — confirm which of the
two the codebase actually uses for teacher-scoping today, since both exist
in the schema, and mirror whichever one is the real source of truth).

The `administrative` role has **no such restriction** — they compile
records across **all** teachers and **all** sections, since their job is to
consolidate what every teacher entered over the school year. So every query
in this new module joins across `students` → `sections` →
`section_teacher_assignments` (to resolve which teacher is/was assigned to
that section for that school year — this is how you determine "sino ang
assigned teachers" per section/year, don't hardcode or guess it) → the
relevant `*_profiles` table, with **no adviser/teacher_id filter** — only
the filters below.

### Required filters
1. **Category** — which record type to view. Since there's no single
   unified "records" table, this filter switches which table the query
   reads from: `Academic`, `Behavioral`, `Developmental`, `Health`,
   `Attendance`, `Achievements` (map directly to `academic_profiles`,
   `behavioral_profiles`, `developmental_profiles`, `health_profiles`,
   `attendance`, `achievements_profiles`). Build one method per category
   (e.g. `getAcademicRecords($schoolYearId, $sectionId)`, etc.) rather than
   trying to force all six into one generic query — they have different
   columns.
2. **School Year** — filter by `school_year_id` (reuse `SchoolYearModel`,
   don't re-query school years ad hoc).
3. **Section** — filter by `section_id`. The section dropdown options
   should be scoped to the selected school year (a section's active
   assignment can change per year via `section_teacher_assignments`).

Each result row should also surface **which teacher recorded/is assigned**
(join `users` on the profile row's `recorded_by`, and/or on
`section_teacher_assignments.teacher_id` for the section) — display the
teacher's name so admin can see whose data they're compiling, even though
they aren't restricted by it.

### Files to build
Follow the same directory convention already used for the teacher module,
under the administrative equivalent (confirm the actual folder name — it
may be `admin`, `administrative`, or something else already established in
the codebase; check where the rollover controller/model from Part 1 lives
and put these alongside it):
- Model — compiled-records queries per category as described above.
- Controller — `index()` accepting `category`, `school_year_id`,
  `section_id` request params, calling the matching model method, guarded
  by `AuthRole::allowOnly(['administrative'])`.
- View — one page with three filter dropdowns (Category, School Year,
  Section) and a results table whose columns adapt to the selected
  category (e.g. Academic shows Subject/Grading Period/Grade; Attendance
  shows Date/Session/Status; Achievements shows Title/Level/Date Received,
  etc.). Use the same table/card markup style as `academic.php`.

### Constraints for this part
- No adviser/teacher-scoping filter anywhere in this module's queries —
  that's the entire point of the role.
- Don't modify the underlying `*_profiles` tables or teacher-side CRUD —
  this module is read-only compilation/reporting, not an editing surface,
  unless a specific edit requirement is stated later.
- Reuse `SchoolYearModel` and existing `Section`-related models if they
  already exist rather than duplicating queries.

## Deliverables
1. Migration/ALTER statement for `students.status` + confirmation it was
   applied.
2. Updated rollover controller/model: archives the old row post-rollover.
3. List of every teacher-side query updated to filter `status = 'active'`.
4. New administrative compiled-records Model/Controller/View with the
   Category/School Year/Section filters described above.