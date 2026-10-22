-- ============================================================
-- Migration: Soft delete for students (Teacher module Trash)
-- Run on: studentprofiledb
-- Date: 2026-10-22
--
-- Students are never hard-deleted from the "Delete" action anymore --
-- they're flagged here, hidden from every active/archived query, and
-- only actually removed (CASCADE handles related records) via Trash's
-- "Delete Permanently", bulk delete, Empty Trash, or the 30-day
-- auto-purge.
-- ============================================================

ALTER TABLE `students`
  ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `status`,
  ADD COLUMN `deleted_by` INT(11) NULL DEFAULT NULL AFTER `deleted_at`,
  ADD KEY `idx_students_deleted_at` (`deleted_at`),
  ADD CONSTRAINT `fk_students_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`);
