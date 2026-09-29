<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../helpers/StudentsAge.php';
require_once __DIR__ . '/AddressService.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    /**
     * Presentation-only helpers for the administrative Learner Profile page
     * (label formatting + .xlsx export) — kept out of the view so the page
     * itself stays declarative markup.
     */

    class LearnerProfileExportService{

        /** Fields that must be written as text — plain digit strings Excel would otherwise mangle. */
        const TEXT_FIELDS = ['LRN', "Father's Contact", "Mother's Contact", "Guardian's Contact"];

        public static function formatLearnerLabel($student){
            $name = trim($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['suffix'] ?? ''));
            $lrn = $student['lrn'] ?? 'no LRN';
            $sy = $student['school_year'] ?? 'no SY';
            $status = ucfirst($student['status'] ?? 'active');
            return "{$name} — {$lrn} — {$sy} ({$status})";
        }

        /**
         * Streams the learner's full profile as an .xlsx download. A real
         * workbook (rather than CSV) so column widths can auto-size to fit
         * the labels and long digit fields (LRN, contact numbers) can be
         * written as explicit text — CSV has no column-width metadata, so
         * Excel falls back to a narrow default and truncates every label
         * in column A, and auto-detects digit strings as numbers.
         * Caller must ensure no output has been sent yet, and should
         * exit() right after.
         */
        public static function exportXlsx($profile, $schoolName = 'San Jose Sur Elementary'){
            $info = $profile['info'];
            $fullName = trim($info['first_name'] . ' ' . ($info['middle_name'] ?? '') . ' ' . $info['last_name'] . ' ' . ($info['suffix'] ?? ''));
            $filename = 'learner-profile-' . preg_replace('/[^A-Za-z0-9]+/', '-', $fullName) . '.xlsx';

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Learner Profile');

            $row = 1;
            self::appendRow($sheet, $row, [$schoolName . ' - Learner Profile'], true);
            self::appendRow($sheet, $row, ['Name', $fullName]);
            self::appendRow($sheet, $row, ['LRN', $info['lrn'] ?? '']);
            self::appendRow($sheet, $row, ['Age / Gender', StudentsAge::calculateAge($info['birth_date']) . ' / ' . $info['gender']]);
            self::appendRow($sheet, $row, ['Grade & Section', ($info['grade_name'] ?? '') . ' - ' . ($info['section_name'] ?? '')]);
            self::appendRow($sheet, $row, ['School Year', $info['school_year'] ?? '']);
            self::appendRow($sheet, $row, ['Status', ucfirst($info['status'])]);
            self::appendRow($sheet, $row, ['Address', AddressService::formatFullAddress($info)]);
            self::appendRow($sheet, $row, ['Age (as of June)', $info['age_as_of_june'] ?? '']);
            self::appendRow($sheet, $row, ['Mother Tongue', $info['mother_tongue'] ?? '']);
            self::appendRow($sheet, $row, ['IP / Ethnic Group', $info['ip_ethnic_group'] ?? '']);
            self::appendRow($sheet, $row, ['Religion', $info['religion'] ?? '']);
            $row++;

            self::writeSection($sheet, $row, 'ACADEMIC RECORDS', ['Subject', 'Grading Period', 'Grade', 'Remarks', 'School Year', 'Recorded By'], $profile['academic'], function($r){
                return [$r['subject_name'], $r['grading_period'], $r['grade'], $r['remarks'] ?? '', $r['school_year'] ?? '', $r['recorded_by_name'] ?? ''];
            }, 'No academic records.');

            self::appendRow($sheet, $row, ['HEALTH PROFILE'], true);
            if(!$profile['health']){
                self::appendRow($sheet, $row, ['No health profile recorded.']);
            }else{
                $h = $profile['health'];
                self::appendRow($sheet, $row, ['Height (cm)', 'Weight (kg)', 'BMI', 'Classification', 'Blood Type', 'Allergies', 'Medical Conditions', 'Vision', 'Hearing', 'Immunization', 'School Year', 'Recorded By'], true);
                self::appendRow($sheet, $row, [$h['height_cm'] ?? '', $h['weight_kg'] ?? '', $h['bmi'] ?? '', $h['bmi_classification'] ?? '', $h['blood_type'] ?? '', $h['allergies'] ?? '', $h['medical_conditions'] ?? '', $h['vision_screening_result'] ?? '', $h['hearing_screening_result'] ?? '', $h['immunization_status'] ?? '', $h['school_year'] ?? '', $h['recorded_by_name'] ?? '']);
            }
            $row++;

            self::writeSection($sheet, $row, 'READING LEVEL RECORDS', ['Assessment Date', 'Reading Level', 'Reading Language', 'Remarks', 'School Year', 'Recorded By'], $profile['reading_level'], function($r){
                return [$r['assessment_date'], $r['reading_level'], $r['reading_language'], $r['remarks'] ?? '', $r['school_year'] ?? '', $r['recorded_by_name'] ?? ''];
            }, 'No reading level records.');

            self::appendRow($sheet, $row, ['PARENT/GUARDIAN INFORMATION'], true);
            if(!$profile['parent_guardian']){
                self::appendRow($sheet, $row, ['No parent/guardian record found.']);
            }else{
                $pg = $profile['parent_guardian'];
                self::appendRow($sheet, $row, ["Father's Name", "Father's Occupation", "Father's Contact", "Mother's Name", "Mother's Occupation", "Mother's Contact", "Guardian's Name", 'Guardian Relationship', "Guardian's Contact", 'Recorded By'], true);
                self::appendRow($sheet, $row, [$pg['father_name'] ?? '', $pg['father_occupation'] ?? '', $pg['father_contact'] ?? '', $pg['mother_name'] ?? '', $pg['mother_occupation'] ?? '', $pg['mother_contact'] ?? '', $pg['guardian_name'] ?? '', $pg['guardian_relationship'] ?? '', $pg['guardian_contact'] ?? '', $pg['recorded_by_name'] ?? '']);
            }

            foreach(range('A', $sheet->getHighestColumn()) as $col){
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }

        /**
         * Writes one row starting at column A, incrementing $row. Values
         * matching a self::TEXT_FIELDS header from the row directly above
         * are written as explicit strings — LRN and contact numbers must
         * never be auto-detected as numbers (drops leading zeros, renders
         * a 12-digit LRN as scientific notation).
         */
        private static function appendRow($sheet, &$row, array $values, $bold = false){
            $sheet->fromArray($values, null, 'A' . $row);
            foreach($values as $colIndex => $value){
                if(!self::looksNumericText($value)){
                    continue;
                }
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValueExplicit($colLetter . $row, (string) $value, DataType::TYPE_STRING);
            }
            if($bold){
                $sheet->getStyle('A' . $row . ':' . $sheet->getHighestColumn() . $row)->getFont()->setBold(true);
            }
            $row++;
        }

        /** A value only needs the explicit-string treatment if it's a long, purely-numeric string (LRN, contact numbers). */
        private static function looksNumericText($value){
            $value = (string) $value;
            return $value !== '' && preg_match('/^\d{4,}$/', $value) === 1;
        }

        private static function writeSection($sheet, &$row, $title, $headers, $rows, $rowMapper, $emptyMessage){
            self::appendRow($sheet, $row, [$title], true);
            self::appendRow($sheet, $row, $headers, true);
            if(empty($rows)){
                self::appendRow($sheet, $row, [$emptyMessage]);
            }else{
                foreach($rows as $r){
                    self::appendRow($sheet, $row, $rowMapper($r));
                }
            }
            $row++;
        }
    }
