-- ============================================================
-- Migration: Add a "position" field for teacher accounts
-- Run on: studentprofiledb
-- Date: 2026-10-20
--
-- Free-text designation (e.g. "Teacher I", "Teacher II", "Master
-- Teacher I") set by administrative staff when creating/editing a
-- teacher account. Lives on the shared `users` table like the other
-- profile columns, but is only ever surfaced in the UI for role =
-- 'teacher'.
-- ============================================================

ALTER TABLE `users`
  ADD COLUMN `position` VARCHAR(100) NULL DEFAULT NULL AFTER `role`;
