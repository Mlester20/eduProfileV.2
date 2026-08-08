<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/teacher/StudentsModel.php';
require_once __DIR__ . '/../models/teacher/ParentGuardianModel.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    /**
     * Bulk-import a teacher's class roster from an .xlsx spreadsheet, plus
     * the matching downloadable template. One row = one student, with the
     * DepEd SF1 fields and an optional parent/guardian record inline.
     * School Year / Grade Level / Section are NOT columns in the sheet —
     * they're picked once in the upload form and applied to every row,
     * same as the single-section auto-fill already used in Add Student.
     */

    class StudentImportService{

        /** Column header => internal field key, in the exact order the template is generated. */
        const COLUMNS = [
            'LRN' => 'lrn',
            'First Name' => 'first_name',
            'Middle Name' => 'middle_name',
            'Last Name' => 'last_name',
            'Suffix' => 'suffix',
            'Birth Date' => 'birth_date',
            'Sex/Gender' => 'gender',
            'Age (as of June)' => 'age_as_of_june',
            'Mother Tongue' => 'mother_tongue',
            'IP/Ethnic Group' => 'ip_ethnic_group',
            'Religion' => 'religion',
            'House Number' => 'house_number',
            'Street' => 'street',
            'Sitio' => 'sitio',
            'Purok' => 'purok',
            'Barangay' => 'barangay',
            'City/Municipality' => 'city_municipality',
            'Province' => 'province',
            "Father's Name" => 'father_name',
            "Father's Occupation" => 'father_occupation',
            "Father's Contact" => 'father_contact',
            "Mother's Name" => 'mother_name',
            "Mother's Occupation" => 'mother_occupation',
            "Mother's Contact" => 'mother_contact',
            "Guardian's Name" => 'guardian_name',
            'Guardian Relationship' => 'guardian_relationship',
            "Guardian's Contact" => 'guardian_contact',
        ];

        const SAMPLE_ROW = [
            '123456789012', 'Juan', 'Santos', 'Dela Cruz', '',
            '2018-06-15', 'Male', '7', 'Tagalog', 'Ilocano', 'Roman Catholic',
            '123', 'Rizal St.', 'Sitio Malaya', 'Purok 3', 'San Jose Sur', 'Rosario', 'Batangas',
            'Pedro Dela Cruz', 'Farmer', '09171234567',
            'Maria Dela Cruz', 'Vendor', '09179876543',
            '', '', '',
        ];

        protected $con;
        protected $studentsModel;
        protected $parentGuardianModel;

        public function __construct($con){
            $this->con = $con;
            $this->studentsModel = new StudentsModel($con);
            $this->parentGuardianModel = new ParentGuardianModel($con);
        }

        public static function generateTemplate(){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Students');

            $headers = array_keys(self::COLUMNS);
            $sheet->fromArray($headers, null, 'A1');
            $sheet->fromArray(self::SAMPLE_ROW, null, 'A2');
            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
            foreach(range('A', $sheet->getHighestColumn()) as $col){
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="student-import-template.xlsx"');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }

        /**
         * Reads the uploaded file into an array of associative rows keyed
         * by internal field name (self::COLUMNS values), skipping the
         * header row. Unknown/extra columns are ignored.
         */

        public function parseFile($filePath){
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if(empty($rows)){
                return [];
            }

            $headerRow = array_shift($rows);
            $fieldByColumnIndex = [];
            foreach($headerRow as $index => $header){
                $header = trim((string) $header);
                if(isset(self::COLUMNS[$header])){
                    $fieldByColumnIndex[$index] = self::COLUMNS[$header];
                }
            }

            $parsed = [];
            foreach($rows as $row){
                if($this->isBlankRow($row)){
                    continue;
                }
                $record = [];
                foreach($fieldByColumnIndex as $index => $field){
                    $value = $row[$index] ?? '';
                    $record[$field] = is_string($value) ? trim($value) : $value;
                }
                $parsed[] = $record;
            }

            return $parsed;
        }

        private function isBlankRow($row){
            foreach($row as $cell){
                if(trim((string) $cell) !== ''){
                    return false;
                }
            }
            return true;
        }

        private function normalizeDate($value){
            if($value instanceof \DateTimeInterface){
                return $value->format('Y-m-d');
            }
            if(is_numeric($value)){
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            }
            $value = trim((string) $value);
            $timestamp = strtotime($value);
            if($value === '' || $timestamp === false){
                return null;
            }
            return date('Y-m-d', $timestamp);
        }

        private function normalizeGender($value){
            $value = strtolower(trim((string) $value));
            if(in_array($value, ['male', 'm'], true)){
                return 'Male';
            }
            if(in_array($value, ['female', 'f'], true)){
                return 'Female';
            }
            return null;
        }

        /**
         * Validates and inserts every parsed row. Each row is its own
         * try/catch so one bad row doesn't abort the whole batch.
         * Returns ['imported' => int, 'errors' => string[]].
         */

        public function import($rows, $schoolYearId, $gradeLevelId, $sectionId, $recordedBy){
            $imported = 0;
            $errors = [];

            foreach($rows as $i => $row){
                $rowNumber = $i + 2; // +1 for 0-index, +1 for the header row
                try{
                    $lrn = $row['lrn'] ?? '';
                    $firstName = $row['first_name'] ?? '';
                    $lastName = $row['last_name'] ?? '';
                    $birthDate = $this->normalizeDate($row['birth_date'] ?? '');
                    $gender = $this->normalizeGender($row['gender'] ?? '');

                    $missing = [];
                    if($lrn === '') $missing[] = 'LRN';
                    if($firstName === '') $missing[] = 'First Name';
                    if($lastName === '') $missing[] = 'Last Name';
                    if($birthDate === null) $missing[] = 'Birth Date';
                    if($gender === null) $missing[] = 'Sex/Gender';
                    if(!empty($missing)){
                        $errors[] = "Row {$rowNumber}: missing or invalid " . implode(', ', $missing) . '.';
                        continue;
                    }

                    if($this->studentsModel->isLrnExists($lrn, $schoolYearId)){
                        $errors[] = "Row {$rowNumber}: LRN {$lrn} already exists for that school year.";
                        continue;
                    }

                    $studentData = [
                        'lrn' => $lrn,
                        'first_name' => $firstName,
                        'middle_name' => $this->nullIfBlank($row['middle_name'] ?? ''),
                        'last_name' => $lastName,
                        'suffix' => $this->nullIfBlank($row['suffix'] ?? ''),
                        'birth_date' => $birthDate,
                        'age_as_of_june' => $this->nullIfBlank($row['age_as_of_june'] ?? ''),
                        'gender' => $gender,
                        'mother_tongue' => $this->nullIfBlank($row['mother_tongue'] ?? ''),
                        'ip_ethnic_group' => $this->nullIfBlank($row['ip_ethnic_group'] ?? ''),
                        'religion' => $this->nullIfBlank($row['religion'] ?? ''),
                        'house_number' => $this->nullIfBlank($row['house_number'] ?? ''),
                        'street' => $this->nullIfBlank($row['street'] ?? ''),
                        'sitio' => $this->nullIfBlank($row['sitio'] ?? ''),
                        'purok' => $this->nullIfBlank($row['purok'] ?? ''),
                        'barangay' => $this->nullIfBlank($row['barangay'] ?? ''),
                        'city_municipality' => $this->nullIfBlank($row['city_municipality'] ?? ''),
                        'province' => $this->nullIfBlank($row['province'] ?? ''),
                        'school_year_id' => $schoolYearId,
                        'grade_level_id' => $gradeLevelId,
                        'section_id' => $sectionId,
                        'recorded_by' => $recordedBy,
                    ];

                    if(!$this->studentsModel->create($studentData)){
                        $errors[] = "Row {$rowNumber}: failed to save student record.";
                        continue;
                    }

                    $newStudentId = $this->con->insert_id;
                    $imported++;

                    if($this->hasParentGuardianData($row)){
                        $this->parentGuardianModel->create([
                            'student_id' => $newStudentId,
                            'recorded_by' => $recordedBy,
                            'father_name' => $row['father_name'] ?? '',
                            'father_occupation' => $row['father_occupation'] ?? '',
                            'father_contact' => $row['father_contact'] ?? '',
                            'mother_name' => $row['mother_name'] ?? '',
                            'mother_occupation' => $row['mother_occupation'] ?? '',
                            'mother_contact' => $row['mother_contact'] ?? '',
                            'guardian_name' => $row['guardian_name'] ?? '',
                            'guardian_relationship' => $row['guardian_relationship'] ?? '',
                            'guardian_contact' => $row['guardian_contact'] ?? '',
                        ]);
                    }
                }catch(Exception $e){
                    error_log("Error importing student row {$rowNumber}: " . $e->getMessage());
                    $errors[] = "Row {$rowNumber}: unexpected error, skipped.";
                }
            }

            return ['imported' => $imported, 'errors' => $errors];
        }

        private function nullIfBlank($value){
            $value = is_string($value) ? trim($value) : $value;
            return ($value === '' || $value === null) ? null : $value;
        }

        private function hasParentGuardianData($row){
            $fields = ['father_name', 'father_occupation', 'father_contact', 'mother_name', 'mother_occupation', 'mother_contact', 'guardian_name', 'guardian_relationship', 'guardian_contact'];
            foreach($fields as $field){
                if(trim((string) ($row[$field] ?? '')) !== ''){
                    return true;
                }
            }
            return false;
        }
    }
