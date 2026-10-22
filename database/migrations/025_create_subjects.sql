-- ============================================================
-- Migration: Subjects, scoped per Grade Level
-- Run on: studentprofiledb
-- Date: 2026-10-21
--
-- Backs the new Admin/Administrative "Subjects" CRUD page (under
-- Academic Setup, next to Grade Levels & Section). DepEd subjects
-- differ by grade (e.g. no Araling Panlipunan in Grade 1-2), so this
-- is scoped to grade_level_id rather than one flat list -- the
-- teacher's Academic Records "Subject" dropdown then only ever shows
-- the subjects that apply to that specific student's grade level.
-- ============================================================

CREATE TABLE `subjects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `grade_level_id` INT(11) NOT NULL,
  `subject_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_subject_per_grade` (`grade_level_id`, `subject_name`),
  CONSTRAINT `fk_subjects_grade_level` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
