Act as an expert Full-Stack Software Engineer and Senior Database Architect. I have provided my raw MySQL database dump (`profilingdb.sql`). I need a fully functional, complete, and production-ready Full-Stack code implementation (Frontend UI + Backend Logic) to complete this project TODAY. Do not give me placeholders or skipped logic.

CORE RULES & ARCHITECTURE BASED ON MY SQL:
1. Active School Year: The foundation is `school_year`. Currently, ID 8 ('2026-2027') is active. All student fetching and data logging must respect this current session ID.
2. The Database Patch: Since my `students` table currently lacks a `section` column and a direct connection to a teacher, write the ALTER TABLE query first to add `section` (VARCHAR) and `assigned_teacher_id` (INT, FK pointing to `users.id`) into the `students` table.
3. Tech Stack: Use raw PHP (with PDO) for backend endpoints, and HTML5 with Tailwind CSS (via CDN) for the Frontend UI. (If you prefer Node.js/Express, write it completely, but PHP is preferred for quick drop-in to XAMPP).

GENERATION REQUIREMENTS (Write the complete code for each file):

TASK 1: DATABASE MIGRATION SCRIPT
- Provide the exact SQL ALTER queries to patch the `students` table with `section` and `assigned_teacher_id`.

TASK 2: BACKEND CONTROLLER / API (`api.php`)
- Write a clean PHP PDO script that handles the following actions via POST/GET:
  - Action A: Fetch all students where `assigned_teacher_id` matches the logged-in teacher's session ID and `enrollment_status = 'Enrolled'`.
  - Action B: Save a new observation/academic tracking record into the `behavior_observations` table (capture student_id, active school_year_id (8), recorded_by (teacher_id), category, date, and the observation text).
  - Action C: Fetch a complete compiled master profile of a student for the Administrative Assistant view (JOINs `students`, `guardians`, and retrieves all historical `behavior_observations` text concatenated or listed cleanly).

TASK 3: FRONTEND UI SCREENS (Single Page App or Clean Layouts using Tailwind CSS)
- Screen 1 (Teacher Dashboard): A clean table showing their assigned students. Clicking a student opens a modal or form to input Academic Tracking & Behavior Observations, then submittable via AJAX/Fetch to the backend.
- Screen 2 (Administrative Assistant Dashboard): A search/select view where the AA can select a student and immediately display their fully compiled Student Information Profile, complete with guardian details, health/basic info, and the timeline of notes inputted by the teacher.

Provide the full, unmodified, copy-pasteable files so I can deploy this right now and proceed to my rest day.

CRITICAL STEP BEFORE WRITING CODE: 
Please read and analyze my entire codebase/workspace files first. I want you to look closely at my existing frontend views, UI templates, layouts, and CSS styles. 

Do not generate a generic UI. I want you to strictly follow my existing Template UI structure, navbar, page wrappers, and components so that the new features look 100% native to my current system.

REQUIREMENTS:
1. Database Patch: Based on the `profilingdb.sql` provided earlier, generate the ALTER TABLE script to add `section` (VARCHAR) and `assigned_teacher_id` (INT, FK) to the `students` table.
2. Full-Stack Feature Injection:
   - Teacher View: Look at how my template renders tables/lists. Implement the fetching of assigned students for School Year ID 8, and inject a modal/form that matches my existing form styles to insert records into `behavior_observations`.
   - Administrative Assistant View: Follow my admin/dashboard template layout. Create the interface where the AA can select a student and view the fully compiled profile (JOINing students, guardians, and a timeline of teacher observations).
3. Backend: Write the complete PHP PDO (or appropriate backend based on my existing controller files) logic, mirroring how I handle sessions and db connections in my current project.

Review my UI files now, and generate the complete full-stack implementation blocks using my exact template style so I can finish this today and go on rest day!

NOTE ON BACKEND: My stack strictly uses MySQLi (not PDO) for database connections and queries. Please ensure all database interactions in the generated code match my existing MySQLi connection variable and syntax (e.g., using $mysqli->prepare, bind_param, and execute) found in my codebase.