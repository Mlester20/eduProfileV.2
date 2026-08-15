<?php

    /**
     * Read-only accessor for the school_settings single-row config table,
     * used by footers/print pages that just need to display the school's
     * identity without pulling in the full admin model/controller. Falls
     * back to the same values the app hardcoded before this table existed,
     * so a page never breaks if the migration hasn't been run yet.
     */

    class SchoolSettings{
        private static $cache = null;

        public static function get($con){
            if(self::$cache !== null){
                return self::$cache;
            }

            $defaults = [
                'school_name' => 'San Jose Sur Elementary',
                'school_id' => '103503',
                'region' => 'Region II',
                'division' => 'Division of Isabela',
                'district' => 'District of Mallig',
            ];

            if(!($con instanceof mysqli)){
                return self::$cache = $defaults;
            }

            try{
                $result = $con->query("SELECT school_name, school_id, region, division, district FROM school_settings WHERE id = 1 LIMIT 1");
                $row = $result ? $result->fetch_assoc() : null;
                return self::$cache = $row ?: $defaults;
            }catch(Exception $e){
                error_log("Error fetching school settings: " . $e->getMessage());
                return self::$cache = $defaults;
            }
        }
    }
