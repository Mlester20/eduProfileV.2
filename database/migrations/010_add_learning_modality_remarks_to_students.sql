-- ============================================================
-- Migration: Add learning_modality and remarks to students
-- Run on: studentprofiledb
-- Date: 2026-08-09
--
-- Both fields are part of DepEd School Form 1 (SF1) — Learning
-- Modality (e.g., Face-to-Face, Modular Distance Learning) and
-- Remarks (free text, e.g. LWD/Transferred/Dropped indicator per
-- the SF1 legend) — needed for the new SF1-style School Register
-- print output in the teacher Students module.
-- ============================================================

ALTER TABLE `students`
  ADD COLUMN `learning_modality` VARCHAR(50) DEFAULT NULL AFTER `province`,
  ADD COLUMN `remarks` VARCHAR(255) DEFAULT NULL AFTER `learning_modality`;
