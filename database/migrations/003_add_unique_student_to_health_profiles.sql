-- ============================================================
-- Migration: Enforce one health profile per student
-- Run on: profilingdb
-- Date: 2026-07-09
--
-- Adds a unique key on student_id so a student can't end up with
-- more than one health_profiles row (records are updated in place
-- instead of re-created per school year).
-- ============================================================

ALTER TABLE `health_profiles`
    ADD UNIQUE KEY `uniq_student_health_profile` (`student_id`);
