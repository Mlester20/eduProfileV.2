-- ============================================================
-- Migration: Make section_teacher_assignments append-only
-- Run on: studentprofiledb
-- Date: 2026-10-16
--
-- section_teacher_assignments had a UNIQUE KEY on (section_id,
-- school_year_id), so SectionsModel::recordAssignmentForActiveYear()'s
-- ON DUPLICATE KEY UPDATE silently overwrote the previous teacher's name
-- whenever a section was reassigned within the same school year (e.g. a
-- teacher leaving mid-year) — losing that history instead of recording it.
--
-- Dropping the unique key lets a new row be appended per reassignment
-- instead of overwriting one in place. Every existing read site resolves
-- "the current teacher for this section/year" by joining this table
-- directly, so a view that picks only the latest row per (section_id,
-- school_year_id) keeps all of those queries correct without touching
-- their SQL — they just need to join the view instead of the raw table.
-- ============================================================

-- `uniq_section_year` is also the only index covering the section_id FK
-- (sta_ibfk_1), so a replacement non-unique index must exist before it can
-- be dropped.
ALTER TABLE `section_teacher_assignments` ADD INDEX `idx_section_year` (`section_id`, `school_year_id`);
ALTER TABLE `section_teacher_assignments` DROP INDEX `uniq_section_year`;

CREATE OR REPLACE VIEW `latest_section_teacher_assignments` AS
SELECT `section_id`, `teacher_id`, `school_year_id`, `created_at`
FROM (
    SELECT
        `section_id`,
        `teacher_id`,
        `school_year_id`,
        `created_at`,
        ROW_NUMBER() OVER (PARTITION BY `section_id`, `school_year_id` ORDER BY `id` DESC) AS `rn`
    FROM `section_teacher_assignments`
) `ranked`
WHERE `rn` = 1;
