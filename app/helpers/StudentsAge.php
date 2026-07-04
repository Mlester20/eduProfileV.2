<?php

    class StudentsAge{
        public static function calculateAge($birthDate){
            $birthDate = new DateTime($birthDate);
            $today = new DateTime('today');
            $age = $birthDate->diff($today)->y;
            return $age;
        }
    }