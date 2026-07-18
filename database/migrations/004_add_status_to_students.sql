-- ============================================================
-- Migration: Add archive status to students
-- Run on: studentprofiledb
-- Date: 2026-07-18
--
-- There is no enrollment_history table in this system. When a
-- student is rolled over to a new school year, they get a new
-- row in `students` (new school_year_id, new recorded_by) while
-- the old row is kept intact so its academic_profiles/attendance/
-- behavioral_profiles/etc. (still foreign-keyed to the old
-- student_id) remain available for compiled/historical reporting.
-- This column marks that old row as no longer current.
-- ============================================================

ALTER TABLE `students`
  ADD COLUMN `status` enum('active','archived') NOT NULL DEFAULT 'active'
  AFTER `recorded_by`;
