-- ============================================================
-- Migration: Cache table for AI-generated at-risk insights
-- Run on: studentprofiledb
-- Date: 2026-07-23
--
-- One cached insight per student per school year. Generated
-- on-demand (not automatically) by GeminiService and stored
-- here so re-viewing the At-Risk Learners page doesn't cost a
-- fresh API call every time.
-- ============================================================

CREATE TABLE `at_risk_insights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `insight_text` text NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_year_unique` (`student_id`, `school_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
