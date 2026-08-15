-- ============================================================
-- Migration: Create school_settings table
-- Run on: studentprofiledb
-- Date: 2026-08-15
--
-- Single-row config table for school identity fields that were
-- previously hardcoded inline across index.php, footer partials,
-- and the SF1/SF2/Compiled Records print pages (San Jose Sur
-- Elementary, School ID 103503, Region II, Division of Isabela,
-- District of Mallig). Admin can now edit these from
-- resources/views/admin/school-settings.php instead of a dev
-- hand-editing PHP. Row id is always 1.
-- ============================================================

CREATE TABLE IF NOT EXISTS `school_settings` (
  `id` INT NOT NULL DEFAULT 1,
  `school_name` VARCHAR(150) NOT NULL DEFAULT 'San Jose Sur Elementary',
  `school_id` VARCHAR(20) DEFAULT NULL,
  `region` VARCHAR(100) DEFAULT NULL,
  `division` VARCHAR(100) DEFAULT NULL,
  `district` VARCHAR(100) DEFAULT NULL,
  `updated_by` INT DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `school_settings` (`id`, `school_name`, `school_id`, `region`, `division`, `district`)
VALUES (1, 'San Jose Sur Elementary', '103503', 'Region II', 'Division of Isabela', 'District of Mallig')
ON DUPLICATE KEY UPDATE id = id;
