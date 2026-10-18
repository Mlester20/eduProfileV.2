-- ============================================================
-- Migration: Add structured Yes/No medical-condition flag to health_profiles
-- Run on: studentprofiledb
-- Date: 2026-10-16
--
-- medical_conditions was free text only, so "does this learner have a
-- medical condition" had to be guessed from whether the field was
-- non-empty -- unreliable for a report (e.g. the Class List health/needs
-- report) that needs a clean Yes/No per learner. has_medical_condition is
-- the actual Yes/No answer from the Health Profile form; medical_conditions
-- stays as the optional "Specify" detail shown only when the answer is Yes.
--
-- Backfill: any existing record with non-empty medical_conditions text is
-- assumed to have been a "Yes" case.
-- ============================================================

ALTER TABLE `health_profiles`
  ADD COLUMN `has_medical_condition` ENUM('Yes','No') NOT NULL DEFAULT 'No' AFTER `allergies`;

UPDATE `health_profiles`
  SET `has_medical_condition` = 'Yes'
  WHERE `medical_conditions` IS NOT NULL AND TRIM(`medical_conditions`) != '';
