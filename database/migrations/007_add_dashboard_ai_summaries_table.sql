-- ============================================================
-- Migration: Cache table for AI-generated dashboard summaries
-- Run on: studentprofiledb
-- Date: 2026-07-23
--
-- One cached school-wide summary per school year. Generated
-- on-demand by GeminiService::generateDashboardSummary() from
-- aggregate counts only (no student names), shown on the
-- administrative dashboard.
-- ============================================================

CREATE TABLE `dashboard_ai_summaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_year_id` int(11) NOT NULL,
  `summary_text` text NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_year_unique` (`school_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
