-- ============================================================
-- Migration: Add 'archived' as a third status value for users
-- Run on: studentprofiledb
-- Date: 2026-10-20
--
-- Separate from active/inactive (which is a login-access toggle) --
-- archived is for a teacher who has left the school for good, hiding
-- them from the default Manage Teachers roster the same way `students`
-- already separates active vs archived learners.
-- ============================================================

ALTER TABLE `users`
  MODIFY COLUMN `status` ENUM('active','inactive','archived') NOT NULL DEFAULT 'active';
