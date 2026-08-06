<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/StudentImportService.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    /**
     * Exports a teacher's own advisees to .xlsx, using the exact same
     * column layout as StudentImportService::COLUMNS — so a teacher can
     * export their roster, edit it, and re-import it through the same
     * template without reshaping anything.
     */

    class StudentExportService{

        public static function exportXlsx($students, $parentGuardianByStudent){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Students');

            $fields = array_values(StudentImportService::COLUMNS);
            $headers = array_keys(StudentImportService::COLUMNS);
            $sheet->fromArray($headers, null, 'A1');

            $rowNumber = 2;
            foreach($students as $student){
                $pg = $parentGuardianByStudent[$student['id']] ?? [];

                $values = [];
                foreach($fields as $field){
                    // Parent/guardian fields live in $pg, everything else in $student.
                    $values[] = $pg[$field] ?? $student[$field] ?? '';
                }
                $sheet->fromArray($values, null, 'A' . $rowNumber);
                $rowNumber++;
            }

            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
            foreach(range('A', $sheet->getHighestColumn()) as $col){
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="my-students-' . date('Y-m-d') . '.xlsx"');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }
