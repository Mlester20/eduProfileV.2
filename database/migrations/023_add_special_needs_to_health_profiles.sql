-- ============================================================
-- Migration: Add a dedicated "Special Needs" flag to Health Profiles
-- Run on: studentprofiledb
-- Date: 2026-10-21
--
-- Separate from has_medical_condition/medical_conditions -- "Condition
-- Noted" in Class List already warns that medical conditions is not an
-- official PWD/SPED determination, so this is its own explicit field
-- rather than overloading that one.
-- ============================================================

ALTER TABLE `health_profiles`
  ADD COLUMN `has_special_needs` ENUM('Yes','No') DEFAULT 'No' AFTER `medical_conditions`,
  ADD COLUMN `special_needs_details` TEXT NULL DEFAULT NULL AFTER `has_special_needs`;
