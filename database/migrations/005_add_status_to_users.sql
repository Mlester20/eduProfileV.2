-- ============================================================
-- Migration: Add active/inactive status to users
-- Run on: studentprofiledb
-- Date: 2026-07-22
--
-- Users are never hard-deleted going forward (that broke FK
-- references in audit_logs.user_id, *_profiles.recorded_by,
-- sections.adviser_id, section_teacher_assignments.teacher_id).
-- Instead an account is deactivated, which also blocks login.
-- ============================================================

ALTER TABLE `users`
  ADD COLUMN `status` enum('active','inactive') NOT NULL DEFAULT 'active'
  AFTER `role`;
