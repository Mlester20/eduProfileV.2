-- ============================================================
-- Migration: Add a profile picture to students
-- Run on: studentprofiledb
-- Date: 2026-10-15
--
-- Mirrors users.profile_picture: a relative path under storage/,
-- uploaded via the teacher's Add/Edit Student modal.
-- ============================================================

ALTER TABLE `students`
  ADD COLUMN `picture` VARCHAR(255) DEFAULT NULL AFTER `suffix`;
