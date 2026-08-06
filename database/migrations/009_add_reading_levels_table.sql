-- ============================================================
-- Migration: Add reading_levels table
-- Run on: studentprofiledb
-- Date: 2026-08-06
--
-- Tracks a learner's reading level assessment (Non-reader /
-- Frustration / Instructional / Independent) per reading
-- language (English / Filipino / MTB) per school year.
-- Mirrors the structure/conventions of behavioral_profiles /
-- developmental_profiles.
-- ============================================================

CREATE TABLE `reading_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `reading_level` enum('Non-reader','Frustration','Instructional','Independent') NOT NULL,
  `reading_language` enum('English','Filipino','MTB') NOT NULL,
  `assessment_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `school_year_id` (`school_year_id`),
  KEY `recorded_by` (`recorded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `reading_levels`
  ADD CONSTRAINT `reading_levels_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reading_levels_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`),
  ADD CONSTRAINT `reading_levels_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);
