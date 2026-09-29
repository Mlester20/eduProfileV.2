<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/StudentImportService.php';

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
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

            // Long digit-only fields (LRN, contact numbers) must be written as
            // explicit strings — left to auto-detect, PhpSpreadsheet stores them
            // as numbers, which drops leading zeros on contact numbers and
            // renders a 12-digit LRN as scientific notation ("1.04E+11").
            $textFields = ['lrn', 'father_contact', 'mother_contact', 'guardian_contact'];

            $rowNumber = 2;
            foreach($students as $student){
                $pg = $parentGuardianByStudent[$student['id']] ?? [];

                $values = [];
                foreach($fields as $field){
                    // Parent/guardian fields live in $pg, everything else in $student.
                    $values[] = $pg[$field] ?? $student[$field] ?? '';
                }
                $sheet->fromArray($values, null, 'A' . $rowNumber);

                foreach($fields as $colIndex => $field){
                    if(in_array($field, $textFields, true) && $values[$colIndex] !== ''){
                        $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                        $sheet->setCellValueExplicit($colLetter . $rowNumber, (string) $values[$colIndex], DataType::TYPE_STRING);
                    }
                }

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
