-- ============================================================
-- Migration: Restore + extend students table
-- Run on: profilingdb
-- Date: 2026-06-24
--
-- Adds back grade_level and enrollment_status (dropped by a
-- prior migration) and adds the new section and
-- assigned_teacher_id columns needed for teacher assignment.
-- ============================================================

ALTER TABLE `students`
    ADD COLUMN `grade_level`          VARCHAR(50)  NULL DEFAULT NULL          AFTER `suffix`,
    ADD COLUMN `enrollment_status`    VARCHAR(20)  NOT NULL DEFAULT 'Enrolled' AFTER `grade_level`,
    ADD COLUMN `section`              VARCHAR(100) NULL DEFAULT NULL          AFTER `enrollment_status`,
    ADD COLUMN `assigned_teacher_id`  INT          NULL DEFAULT NULL          AFTER `section`;

-- Foreign key linking each student to their assigned teacher
ALTER TABLE `students`
    ADD CONSTRAINT `fk_students_assigned_teacher`
        FOREIGN KEY (`assigned_teacher_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

-- Optional: verify
-- SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT
-- FROM information_schema.COLUMNS
-- WHERE TABLE_SCHEMA = 'profilingdb' AND TABLE_NAME = 'students'
-- AND COLUMN_NAME IN ('grade_level','enrollment_status','section','assigned_teacher_id');
