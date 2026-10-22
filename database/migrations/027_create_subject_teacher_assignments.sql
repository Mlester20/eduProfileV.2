-- ============================================================
-- Migration: Subject-to-Teacher assignments
-- Run on: studentprofiledb
-- Date: 2026-10-21
--
-- A subject (scoped to one grade level) can be taught by more than one
-- teacher (e.g. 3 sections of Grade 1, 3 different advisers), and each
-- teacher's assignment is independent -- assigning "Filipino" to one
-- Grade 1 teacher does NOT automatically assign it to the other two.
-- The teacher's Academic Records "Subject" dropdown is filtered to
-- only the subjects assigned to them here (intersected with the
-- specific student's grade level).
-- ============================================================

CREATE TABLE `subject_teacher_assignments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `subject_id` INT(11) NOT NULL,
  `teacher_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_subject_teacher` (`subject_id`, `teacher_id`),
  CONSTRAINT `fk_sta_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sta_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
