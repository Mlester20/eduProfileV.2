<?php

    /**
     * Presentation-only helper for composing a student's structured address
     * (house #, street, sitio, purok, barangay, city/municipality, province —
     * added by the DepEd SF1 fields migration) into a single display string.
     * Falls back to the legacy free-text `address` column when none of the
     * structured fields are filled in, so older records still show something.
     */

    class AddressService{

        public static function formatFullAddress($student){
            $parts = [
                trim(($student['house_number'] ?? '') . ' ' . ($student['street'] ?? '')),
                $student['sitio'] ?? '',
                $student['purok'] ?? '',
                $student['barangay'] ?? '',
                $student['city_municipality'] ?? '',
                $student['province'] ?? '',
            ];
            $parts = array_filter(array_map('trim', $parts), function($part){
                return $part !== '';
            });

            if(empty($parts)){
                return trim($student['address'] ?? '');
            }

            return implode(', ', $parts);
        }

        /**
         * Same composition as formatFullAddress(), but for a prefixed set of
         * columns -- e.g. parents_guardians' father_/mother_/guardian_
         * house_number/street/sitio/purok/barangay/city_municipality/province.
         * No legacy free-text fallback here; parents/guardians never had one.
         */
        public static function formatPersonAddress($data, $prefix){
            $parts = [
                trim(($data[$prefix . 'house_number'] ?? '') . ' ' . ($data[$prefix . 'street'] ?? '')),
                $data[$prefix . 'sitio'] ?? '',
                $data[$prefix . 'purok'] ?? '',
                $data[$prefix . 'barangay'] ?? '',
                $data[$prefix . 'city_municipality'] ?? '',
                $data[$prefix . 'province'] ?? '',
            ];
            $parts = array_filter(array_map('trim', $parts), function($part){
                return $part !== '';
            });

            return implode(', ', $parts);
        }
    }
