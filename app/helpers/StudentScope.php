<?php

    /**
     * Named fragments for the `s.status = '...'` condition that's already
     * copy-pasted into nearly every query that joins the students table
     * (as `s`) -- centralized here so adding the Trash feature's
     * `deleted_at IS NULL` check can't be silently missed by some future
     * query that copies an old one instead of this constant. Doesn't
     * change what any existing query selects, just names the fragment.
     *
     * Every query using these must alias the students table as `s`.
     */

    class StudentScope{
        const ACTIVE = "s.status = 'active' AND s.deleted_at IS NULL";
        const ARCHIVED = "s.status = 'archived' AND s.deleted_at IS NULL";
        const NOT_TRASHED = "s.deleted_at IS NULL";
        const TRASHED = "s.deleted_at IS NOT NULL";

        /**
         * Retention window for Trash, in days, before a student is
         * eligible for auto-purge. Configurable via .env
         * (TRASH_RETENTION_DAYS) so it never needs a code change.
         */
        public static function retentionDays(){
            if(!isset($_ENV['TRASH_RETENTION_DAYS'])){
                require_once __DIR__ . '/../../vendor/autoload.php';
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
                $dotenv->safeLoad();
            }
            $days = (int) ($_ENV['TRASH_RETENTION_DAYS'] ?? 30);
            return $days > 0 ? $days : 30;
        }
    }
