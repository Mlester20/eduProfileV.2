-- ============================================================
-- Migration: Add School Head name to school_settings
-- Run on: studentprofiledb
-- Date: 2026-08-19
--
-- The SF1/SF2 (teacher) and Compiled Records (administrative) print
-- templates each have a "Certified Correct... (Signature of School
-- Head over Printed Name)" signature line, but school_settings never
-- had a field to source that name from -- it was always left blank.
-- ============================================================

ALTER TABLE `school_settings`
  ADD COLUMN `school_head` VARCHAR(150) DEFAULT NULL AFTER `district`;
