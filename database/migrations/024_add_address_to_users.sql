-- ============================================================
-- Migration: Structured Province/City/Barangay for teacher accounts
-- Run on: studentprofiledb
-- Date: 2026-10-21
--
-- Replaces the free-text Address field in Manage Teachers with the
-- same cascading Province -> City/Municipality -> Barangay picker
-- already used for students/parents-guardians -- just the three top
-- levels, no house#/street/sitio/purok breakdown for teachers.
-- The old `address` column is left in place (untouched, legacy data
-- stays readable) but the UI no longer reads/writes it going forward.
-- ============================================================

ALTER TABLE `users`
  ADD COLUMN `province` VARCHAR(100) NULL DEFAULT NULL AFTER `address`,
  ADD COLUMN `city_municipality` VARCHAR(100) NULL DEFAULT NULL AFTER `province`,
  ADD COLUMN `barangay` VARCHAR(100) NULL DEFAULT NULL AFTER `city_municipality`;
