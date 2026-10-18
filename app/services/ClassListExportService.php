<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    /**
     * Exports the Class List exactly as currently filtered (school year +
     * section) — same "scope follows the active filter" approach as
     * FeedingProgramExportService.
     */

    class ClassListExportService{

        private static function bmiLabel($classification){
            switch($classification){
                case 'Severely Wasted':
                case 'Wasted':
                case 'Obese':
                case 'Overweight':
                case 'Normal':
                    return $classification;
                default:
                    return 'Not yet recorded';
            }
        }

        public static function exportXlsx($classList, $sectionLabel = 'All Sections'){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Class List');

            $lrnColIndex = 1;
            $headers = ['#', 'LRN', 'Student', 'Grade & Section', 'Adviser', 'Nutritional Status', 'Condition Noted', 'Details', 'School Year'];
            $sheet->fromArray($headers, null, 'A1');

            $rowNumber = 2;
            foreach($classList as $index => $learner){
                $studentName = trim($learner['student_first_name'] . ' ' . ($learner['student_middle_name'] ?? '') . ' ' . $learner['student_last_name'] . ' ' . ($learner['student_suffix'] ?? ''));
                $sectionDisplay = trim(($learner['grade_name'] ?? '') . ' - ' . ($learner['section_name'] ?? ''));
                $hasCondition = ($learner['has_medical_condition'] ?? 'No') === 'Yes';

                $values = [
                    $index + 1,
                    $learner['lrn'] ?? '',
                    $studentName,
                    $sectionDisplay,
                    $learner['assigned_teacher_name'] ?? '',
                    self::bmiLabel($learner['bmi_classification'] ?? null),
                    $hasCondition ? 'Yes' : 'No',
                    $hasCondition ? trim($learner['medical_conditions'] ?? '') : '',
                    $learner['school_year'] ?? '',
                ];

                $sheet->fromArray($values, null, 'A' . $rowNumber);

                if($values[$lrnColIndex] !== ''){
                    $lrnColLetter = Coordinate::stringFromColumnIndex($lrnColIndex + 1);
                    $sheet->setCellValueExplicit($lrnColLetter . $rowNumber, (string) $values[$lrnColIndex], DataType::TYPE_STRING);
                }

                $rowNumber++;
            }

            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
            foreach(range('A', $sheet->getHighestColumn()) as $col){
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $safeSectionLabel = preg_replace('/[^A-Za-z0-9 _-]/', '', $sectionLabel);
            $filename = 'class-list-' . trim($safeSectionLabel ?: 'all-sections') . '-' . date('Y-m-d') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }
