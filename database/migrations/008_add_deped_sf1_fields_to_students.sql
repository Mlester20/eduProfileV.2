-- ============================================================
-- Migration: Add DepEd SF1-style demographic/address fields to students
-- Run on: studentprofiledb
-- Date: 2026-07-24
--
-- Client-provided student profile field list included several items
-- with no existing column: age as of the 1st Friday of June, mother
-- tongue (Grade 1-3 only), IP/ethnic group, religion, and a fully
-- structured address (house #, street, sitio, purok, barangay,
-- city/municipality, province). Name (first_name/middle_name/
-- last_name/suffix), sex/gender (gender), and birth date (birth_date)
-- already have equivalent columns and are left untouched.
-- ============================================================

ALTER TABLE `students`
  ADD COLUMN `age_as_of_june` TINYINT(3) UNSIGNED DEFAULT NULL COMMENT 'Age as of the 1st Friday of June' AFTER `birth_date`,
  ADD COLUMN `mother_tongue` VARCHAR(50) DEFAULT NULL COMMENT 'Grade 1 to Grade 3 only' AFTER `gender`,
  ADD COLUMN `ip_ethnic_group` VARCHAR(100) DEFAULT NULL COMMENT 'Indigenous People / ethnic group' AFTER `mother_tongue`,
  ADD COLUMN `religion` VARCHAR(100) DEFAULT NULL AFTER `ip_ethnic_group`,
  ADD COLUMN `house_number` VARCHAR(20) DEFAULT NULL AFTER `address`,
  ADD COLUMN `street` VARCHAR(100) DEFAULT NULL AFTER `house_number`,
  ADD COLUMN `sitio` VARCHAR(100) DEFAULT NULL AFTER `street`,
  ADD COLUMN `purok` VARCHAR(100) DEFAULT NULL AFTER `sitio`,
  ADD COLUMN `barangay` VARCHAR(100) DEFAULT NULL AFTER `purok`,
  ADD COLUMN `city_municipality` VARCHAR(100) DEFAULT NULL AFTER `barangay`,
  ADD COLUMN `province` VARCHAR(100) DEFAULT NULL AFTER `city_municipality`;

-- ------------------------------------------------------------
-- NOTE (informational only — nothing below this line is executed
-- as part of this migration; no columns are dropped or modified):
--
-- The existing `address` column (varchar(50), a single free-text
-- field) now overlaps with the structured house_number/street/sitio/
-- purok/barangay/city_municipality/province fields added above.
-- Suggest migrating existing `address` values into the new structured
-- fields where possible (or keeping `address` around as a legacy/
-- display-only "full address" fallback for old records), then
-- deprecating it once the UI is updated to read/write the structured
-- fields directly. Flagging only — `address` is intentionally not
-- dropped or altered here.
-- ------------------------------------------------------------
