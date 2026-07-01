Here's a ready-to-use prompt you can hand to Claude to build out `StudentService`:

---

**Prompt:**

I'm working on a PHP MVC project (custom framework, no Laravel/Symfony). I need you to build out a `StudentService` class that sits between the `StudentsController` and `StudentsModel`, handling business logic — specifically formatting the student's full name and paginating the student list.

**Context files:**

1. `StudentsModel` (in `app/models/teacher/StudentsModel.php`) — has `index()`, `create($data)`, `update($id, $data)`, `delete($id)` methods that talk to the DB via mysqli. `index()` currently returns ALL students with a JOIN query (no pagination, no name concatenation in SQL).
2. `StudentsController` (in `app/controllers/teacher/StudentsController.php`) — wraps the model, currently just calls `$this->model->index()` directly.
3. `StudentService` stub (in `app/services/StudentService.php`) — currently empty, extends `Model`, only has `protected $students = 'students';`.
4. The view (`students.php`) loops over `$students` and prints `$student['student_name']` directly — but the model doesn't currently produce a `student_name` field.

**Requirements:**

1. **Full name formatting must happen in PHP, NOT in SQL.**
   - Do NOT use `CONCAT()` in any SQL query.
   - `StudentService` should have a method (e.g. `formatFullName($student)` or `getFullName($first, $middle, $last, $suffix)`) that builds the full name in PHP by combining `first_name`, `middle_name`, `last_name`, and `suffix`.
   - Handle edge cases: empty/null `middle_name` (don't leave double spaces), empty/null `suffix` (don't append stray commas or spaces), and proper capitalization if needed.
   - Suggested format: `Last, First Middle Suffix` or `First Middle Last, Suffix` — pick one and apply it consistently (ask me if unsure).

2. **Pagination — 10 entries per page.**
   - `StudentService` (or `StudentsModel`, whichever is cleaner) should expose a method like `getPaginatedStudents($page = 1, $perPage = 10)`.
   - Use SQL `LIMIT` and `OFFSET` for the actual page of results (do not fetch all rows and slice in PHP).
   - Also return total row count (a separate `COUNT(*)` query) so the view can render page numbers.
   - Return a structured array/object like:
     ```php
     [
         'data' => [...],       // 10 students for this page, each with 'full_name' added
         'total' => 45,
         'per_page' => 10,
         'current_page' => 1,
         'total_pages' => 5,
     ]
     ```

3. **Wire it together:**
   - Update `StudentsController::index()` to call the service instead of the model directly, passing along a `?page=` query param.
   - Show how `StudentsModel::index()` should change to support `LIMIT`/`OFFSET` via prepared statement parameters (don't break the existing JOINs with `school_year`, `sections`, `grade_levels`, `users`).
   - Update the view to loop over `$result['data']`, print `$student['full_name']`, and render simple pagination links (Bootstrap-style `<nav><ul class="pagination">`) based on `$result['total_pages']` and `$result['current_page']`.

**Constraints:**
- Keep the existing mysqli + prepared statement style (no PDO, no query builder).
- Keep the existing `try/catch` + `error_log()` pattern used elsewhere in the codebase.
- Don't break existing methods (`create`, `update`, `delete`) — only touch what's needed for full name + pagination.

Please give me the updated `StudentService`, the updated `StudentsModel::index()`, the updated `StudentsController::index()`, and the updated pagination markup for the view.