-- ============================================================
-- Migration: Add address fields to parents_guardians (Father/Mother/Guardian)
-- Run on: studentprofiledb
-- Date: 2026-10-20
--
-- Mirrors the same House #/Street/Sitio/Purok + Province/City-Municipality
-- /Barangay breakdown already used for the student's own address on the
-- `students` table, so the teacher's Add/Edit Student modal can offer the
-- same cascading PSGC picker for each of Father, Mother, and Guardian.
--
-- Ages (fathers_age/mothers_age/guardians_age) already exist on this table
-- from an earlier migration -- this one only adds the address columns.
-- ============================================================

ALTER TABLE `parents_guardians`
  ADD COLUMN `father_house_number` VARCHAR(20) DEFAULT NULL AFTER `father_contact`,
  ADD COLUMN `father_street` VARCHAR(100) DEFAULT NULL AFTER `father_house_number`,
  ADD COLUMN `father_sitio` VARCHAR(100) DEFAULT NULL AFTER `father_street`,
  ADD COLUMN `father_purok` VARCHAR(100) DEFAULT NULL AFTER `father_sitio`,
  ADD COLUMN `father_barangay` VARCHAR(100) DEFAULT NULL AFTER `father_purok`,
  ADD COLUMN `father_city_municipality` VARCHAR(100) DEFAULT NULL AFTER `father_barangay`,
  ADD COLUMN `father_province` VARCHAR(100) DEFAULT NULL AFTER `father_city_municipality`,

  ADD COLUMN `mother_house_number` VARCHAR(20) DEFAULT NULL AFTER `mother_contact`,
  ADD COLUMN `mother_street` VARCHAR(100) DEFAULT NULL AFTER `mother_house_number`,
  ADD COLUMN `mother_sitio` VARCHAR(100) DEFAULT NULL AFTER `mother_street`,
  ADD COLUMN `mother_purok` VARCHAR(100) DEFAULT NULL AFTER `mother_sitio`,
  ADD COLUMN `mother_barangay` VARCHAR(100) DEFAULT NULL AFTER `mother_purok`,
  ADD COLUMN `mother_city_municipality` VARCHAR(100) DEFAULT NULL AFTER `mother_barangay`,
  ADD COLUMN `mother_province` VARCHAR(100) DEFAULT NULL AFTER `mother_city_municipality`,

  ADD COLUMN `guardian_house_number` VARCHAR(20) DEFAULT NULL AFTER `guardian_contact`,
  ADD COLUMN `guardian_street` VARCHAR(100) DEFAULT NULL AFTER `guardian_house_number`,
  ADD COLUMN `guardian_sitio` VARCHAR(100) DEFAULT NULL AFTER `guardian_street`,
  ADD COLUMN `guardian_purok` VARCHAR(100) DEFAULT NULL AFTER `guardian_sitio`,
  ADD COLUMN `guardian_barangay` VARCHAR(100) DEFAULT NULL AFTER `guardian_purok`,
  ADD COLUMN `guardian_city_municipality` VARCHAR(100) DEFAULT NULL AFTER `guardian_barangay`,
  ADD COLUMN `guardian_province` VARCHAR(100) DEFAULT NULL AFTER `guardian_city_municipality`;
