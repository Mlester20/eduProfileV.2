-- ============================================================
-- Migration: Forgot Password OTP support
-- Run on: studentprofiledb
-- Date: 2026-10-20
--
-- Stores hashed 6-digit verification codes for the password
-- reset flow. Codes are hashed (password_hash), never stored
-- in plaintext, and expire 10 minutes after issue.
-- ============================================================

CREATE TABLE IF NOT EXISTS `password_reset_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `code_hash` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME NULL DEFAULT NULL,
  `requested_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  KEY `idx_password_reset_codes_user` (`user_id`),
  CONSTRAINT `fk_password_reset_codes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
