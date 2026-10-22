-- ============================================================
-- Migration: Allow audit_logs.user_id to be NULL
-- Run on: studentprofiledb
-- Date: 2026-10-22
--
-- The Trash feature's auto-purge isn't triggered by any specific user
-- (it runs opportunistically whenever anyone's Trash page loads, or via
-- the standalone purge-trash.php script) -- AuditLogs::log() is called
-- with role='system' and no user_id for that entry. user_id was NOT
-- NULL with no default, so that insert was silently failing. Every
-- other caller of AuditLogs::log() already passes a real user id and
-- is unaffected.
-- ============================================================

ALTER TABLE `audit_logs`
  MODIFY COLUMN `user_id` INT(11) NULL DEFAULT NULL;
