<?php

    /**
     * Presentation-only helper for the administrative Learner Profile page
     * (label formatting) — kept out of the view so the page itself stays
     * declarative markup.
     */

    class LearnerProfileExportService{

        public static function formatLearnerLabel($student){
            $name = trim($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['suffix'] ?? ''));
            $lrn = $student['lrn'] ?? 'no LRN';
            $sy = $student['school_year'] ?? 'no SY';
            $status = ucfirst($student['status'] ?? 'active');
            return "{$name} — {$lrn} — {$sy} ({$status})";
        }
    }
