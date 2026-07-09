-- ============================================================
-- Migration: Split attendance into Morning/Afternoon sessions
-- Run on: profilingdb
-- Date: 2026-07-08
--
-- Adds a `session` column so a student can have up to 2 attendance
-- rows per day (one per session), and a unique key to prevent
-- duplicate (student_id, attendance_date, session) rows.
-- ============================================================

ALTER TABLE `attendance`
    ADD COLUMN `session` ENUM('Morning','Afternoon') NOT NULL AFTER `attendance_date`;

ALTER TABLE `attendance`
    ADD UNIQUE KEY `uniq_student_date_session` (`student_id`, `attendance_date`, `session`);

-- Optional: verify
-- SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
-- FROM information_schema.COLUMNS
-- WHERE TABLE_SCHEMA = 'profilingdb' AND TABLE_NAME = 'attendance'
-- AND COLUMN_NAME = 'session';
