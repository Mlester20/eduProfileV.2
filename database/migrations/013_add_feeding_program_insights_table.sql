-- ============================================================
-- Migration: Cache table for AI-generated feeding program insights
-- Run on: studentprofiledb
-- Date: 2026-10-11
--
-- One cached insight per student per school year, same shape as
-- at_risk_insights. Generated on-demand (not automatically) by
-- GeminiService::generateFeedingProgramInsight() for learners
-- flagged Severely Wasted / Wasted on their Health Profile, and
-- stored here so re-viewing the Feeding Program page doesn't cost
-- a fresh API call every time.
-- ============================================================

CREATE TABLE `feeding_program_insights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `insight_text` text NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_year_unique` (`student_id`, `school_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
