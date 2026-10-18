<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    /**
     * Exports the Feeding Program Priority List exactly as currently
     * filtered (school year + section) — the caller passes in the same
     * already-filtered $learners array the page itself renders, so "export
     * this one section" vs "export all sections" falls out of whatever
     * filter is active on the page, no separate logic needed here.
     */

    class FeedingProgramExportService{

        public static function exportXlsx($learners, $sectionLabel = 'All Sections', $includeTeacherColumn = false){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Feeding Program');

            $lrnColIndex = 1; // 0-based position of the LRN column, for the explicit-string fix below
            $headers = ['#', 'LRN', 'Student', 'Grade & Section'];
            if($includeTeacherColumn){
                $headers[] = 'Adviser';
            }
            $headers[] = 'BMI';
            $headers[] = 'Status';
            $headers[] = 'School Year';
            $sheet->fromArray($headers, null, 'A1');

            $rowNumber = 2;
            foreach($learners as $index => $learner){
                $studentName = trim($learner['student_first_name'] . ' ' . ($learner['student_middle_name'] ?? '') . ' ' . $learner['student_last_name'] . ' ' . ($learner['student_suffix'] ?? ''));
                $sectionDisplay = trim(($learner['grade_name'] ?? '') . ' - ' . ($learner['section_name'] ?? ''));

                $values = [$index + 1, $learner['lrn'] ?? '', $studentName, $sectionDisplay];
                if($includeTeacherColumn){
                    $values[] = $learner['assigned_teacher_name'] ?? '';
                }
                $values[] = $learner['bmi'] ?? '';
                $values[] = $learner['bmi_classification'] ?? '';
                $values[] = $learner['school_year'] ?? '';

                $sheet->fromArray($values, null, 'A' . $rowNumber);

                // Written as an explicit string so the 12-digit LRN doesn't get
                // auto-converted to scientific notation (same fix as StudentExportService).
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
            $filename = 'feeding-program-' . trim($safeSectionLabel ?: 'all-sections') . '-' . date('Y-m-d') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }
