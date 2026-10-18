-- ============================================================
-- Migration: Add contact number, address, birth date, and sex to users
-- Run on: studentprofiledb
-- Date: 2026-10-16
--
-- Backs the administrative "Manage Teacher" Add/Edit forms, which need
-- these fields on top of full_name/email. Nullable since existing accounts
-- (and admin-created users via admin/UsersController.php) don't collect
-- them.
-- ============================================================

ALTER TABLE `users`
  ADD COLUMN `contact_number` VARCHAR(20) DEFAULT NULL AFTER `email`,
  ADD COLUMN `address` VARCHAR(255) DEFAULT NULL AFTER `contact_number`,
  ADD COLUMN `birth_date` DATE DEFAULT NULL AFTER `address`,
  ADD COLUMN `sex` ENUM('Male','Female') DEFAULT NULL AFTER `birth_date`;
