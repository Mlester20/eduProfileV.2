<?php
require_once __DIR__ . '/../helpers/StudentsAge.php';

    /**
     * Presentation-only helpers for the administrative Learner Profile page
     * (label formatting + CSV export) — kept out of the view so the page
     * itself stays declarative markup.
     */

    class LearnerProfileExportService{

        public static function formatLearnerLabel($student){
            $name = trim($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['suffix'] ?? ''));
            $lrn = $student['lrn'] ?? 'no LRN';
            $sy = $student['school_year'] ?? 'no SY';
            $status = ucfirst($student['status'] ?? 'active');
            return "{$name} — {$lrn} — {$sy} ({$status})";
        }

        /**
         * Streams the learner's full profile as a CSV download. Caller must
         * ensure no output has been sent yet, and should exit() right after.
         */
        public static function exportCsv($profile){
            $info = $profile['info'];
            $fullName = trim($info['first_name'] . ' ' . ($info['middle_name'] ?? '') . ' ' . $info['last_name'] . ' ' . ($info['suffix'] ?? ''));
            $filename = 'learner-profile-' . preg_replace('/[^A-Za-z0-9]+/', '-', $fullName) . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['San Jose Sur Elementary - Learner Profile']);
            fputcsv($out, ['Name', $fullName]);
            fputcsv($out, ['LRN', $info['lrn'] ?? '']);
            fputcsv($out, ['Age / Gender', StudentsAge::calculateAge($info['birth_date']) . ' / ' . $info['gender']]);
            fputcsv($out, ['Grade & Section', ($info['grade_name'] ?? '') . ' - ' . ($info['section_name'] ?? '')]);
            fputcsv($out, ['School Year', $info['school_year'] ?? '']);
            fputcsv($out, ['Status', ucfirst($info['status'])]);
            fputcsv($out, ['Address', $info['address'] ?? '']);
            fputcsv($out, []);

            self::writeSection($out, 'ACADEMIC RECORDS', ['Subject', 'Grading Period', 'Grade', 'Remarks', 'School Year', 'Recorded By'], $profile['academic'], function($r){
                return [$r['subject_name'], $r['grading_period'], $r['grade'], $r['remarks'] ?? '', $r['school_year'] ?? '', $r['recorded_by_name'] ?? ''];
            }, 'No academic records.');

            self::writeSection($out, 'ATTENDANCE RECORDS', ['Date', 'Session', 'Status', 'Remarks', 'Recorded By'], $profile['attendance'], function($r){
                return [$r['attendance_date'], $r['session'], $r['status'], $r['remarks'] ?? '', $r['recorded_by_name'] ?? ''];
            }, 'No attendance records.');

            self::writeSection($out, 'BEHAVIORAL RECORDS', ['Date', 'Category', 'Observation', 'Intervention', 'Remarks', 'Recorded By'], $profile['behavioral'], function($r){
                return [$r['observation_date'], $r['category'], $r['observation'], $r['intervention'] ?? '', $r['remarks'] ?? '', $r['recorded_by_name'] ?? ''];
            }, 'No behavioral records.');

            self::writeSection($out, 'DEVELOPMENTAL RECORDS', ['Domain', 'Observation', 'Recommendation', 'School Year', 'Recorded By'], $profile['developmental'], function($r){
                return [$r['domain'], $r['observation'], $r['recommendation'] ?? '', $r['school_year'] ?? '', $r['recorded_by_name'] ?? ''];
            }, 'No developmental records.');

            fputcsv($out, ['HEALTH PROFILE']);
            if(!$profile['health']){
                fputcsv($out, ['No health profile recorded.']);
            }else{
                $h = $profile['health'];
                fputcsv($out, ['Height (cm)', 'Weight (kg)', 'BMI', 'Classification', 'Blood Type', 'Allergies', 'Medical Conditions', 'Vision', 'Hearing', 'Immunization', 'School Year', 'Recorded By']);
                fputcsv($out, [$h['height_cm'] ?? '', $h['weight_kg'] ?? '', $h['bmi'] ?? '', $h['bmi_classification'] ?? '', $h['blood_type'] ?? '', $h['allergies'] ?? '', $h['medical_conditions'] ?? '', $h['vision_screening_result'] ?? '', $h['hearing_screening_result'] ?? '', $h['immunization_status'] ?? '', $h['school_year'] ?? '', $h['recorded_by_name'] ?? '']);
            }
            fputcsv($out, []);

            self::writeSection($out, 'ACHIEVEMENT RECORDS', ['Title', 'Category', 'Level', 'Date Received', 'Awarding Body', 'Recorded By'], $profile['achievements'], function($r){
                return [$r['title'], $r['category'], $r['level'], $r['date_received'], $r['awarding_body'] ?? '', $r['recorded_by_name'] ?? ''];
            }, 'No achievement records.', false);

            fclose($out);
        }

        private static function writeSection($out, $title, $headers, $rows, $rowMapper, $emptyMessage, $trailingBlankLine = true){
            fputcsv($out, [$title]);
            fputcsv($out, $headers);
            if(empty($rows)){
                fputcsv($out, [$emptyMessage]);
            }else{
                foreach($rows as $r){
                    fputcsv($out, $rowMapper($r));
                }
            }
            if($trailingBlankLine){
                fputcsv($out, []);
            }
        }
    }