-- ============================================================
-- Migration: Track who created/last edited each Subject
-- Run on: studentprofiledb
-- Date: 2026-10-21
--
-- Audit trail column, same pattern as academic_profiles.recorded_by --
-- references the admin/administrative user who added or last updated
-- the row.
-- ============================================================

ALTER TABLE `subjects`
  ADD COLUMN `recorded_by` INT(11) NOT NULL AFTER `subject_name`,
  ADD CONSTRAINT `fk_subjects_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);
