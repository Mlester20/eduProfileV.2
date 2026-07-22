<?php

    /**
     * Calls the Gemini API to turn computed metrics into short narrative
     * text — either a per-student at-risk insight or a school-wide
     * dashboard summary. Both only ever receive aggregate counts (grade
     * level, section, failing/absence/disciplinary counts) — never a
     * student's name — so no personally-identifiable data leaves the
     * server.
     */

    class GeminiService{
        const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

        private static function buildInsightPrompt($metrics){
            $lines = [];
            $lines[] = "You are assisting a school administrator reviewing a learner flagged as at-risk.";
            $lines[] = "Grade level: " . ($metrics['grade_name'] ?? 'unknown');
            $lines[] = "Section: " . ($metrics['section_name'] ?? 'unknown');
            $lines[] = "Failing subjects this school year: " . (int) ($metrics['failing_count'] ?? 0);
            $lines[] = "Recorded absences this school year: " . (int) ($metrics['absence_count'] ?? 0);
            $lines[] = "Disciplinary behavioral incidents this school year: " . (int) ($metrics['disciplinary_count'] ?? 0);
            $lines[] = "Write a brief (2-3 sentence), professional, actionable insight for the administrator. Interpret what these numbers suggest and recommend a follow-up or intervention. Do not simply restate the numbers. Do not use the learner's name (you were not given one).";
            return implode("\n", $lines);
        }

        private static function buildSummaryPrompt($metrics){
            $lines = [];
            $lines[] = "You are assisting a school administrator with a high-level overview of their school for the current school year.";
            $lines[] = "School year: " . ($metrics['school_year'] ?? 'unknown');
            $lines[] = "Active learners: " . (int) ($metrics['active_learners'] ?? 0);
            $lines[] = "Archived learners: " . (int) ($metrics['archived_learners'] ?? 0);
            $lines[] = "Total sections: " . (int) ($metrics['total_sections'] ?? 0);
            $lines[] = "Sections without an assigned adviser: " . (int) ($metrics['sections_without_adviser'] ?? 0);
            $lines[] = "Learners currently flagged at-risk: " . (int) ($metrics['at_risk_count'] ?? 0);
            $lines[] = "  - of which failing academically: " . (int) ($metrics['at_risk_failing'] ?? 0);
            $lines[] = "  - of which have chronic absences: " . (int) ($metrics['at_risk_absences'] ?? 0);
            $lines[] = "  - of which have repeated disciplinary incidents: " . (int) ($metrics['at_risk_disciplinary'] ?? 0);

            $byLocation = $metrics['at_risk_by_location'] ?? [];
            if(!empty($byLocation)){
                $lines[] = "At-risk learners by grade level and section:";
                foreach($byLocation as $line){
                    $lines[] = "  - " . $line;
                }
            }

            $lines[] = "Write a brief (3-4 sentence), professional executive summary for the administrator. Explicitly name the specific grade level(s) and section(s) that have at-risk learners (from the breakdown above) so the administrator immediately knows where to focus, rather than speaking only in generalities. Highlight the most important pattern or concern and recommend one focus area for the coming weeks. Do not simply restate the raw counts. Do not mention any individual learner (you were not given any names).";
            return implode("\n", $lines);
        }

        private static function callApi($prompt){
            $secretsFile = __DIR__ . '/../../database/config/secrets.php';
            if(!defined('GEMINI_API_KEY') && file_exists($secretsFile)){
                require_once $secretsFile;
            }
            if(!defined('GEMINI_API_KEY') || GEMINI_API_KEY === '' || GEMINI_API_KEY === 'PASTE_YOUR_KEY_HERE'){
                error_log("GeminiService: GEMINI_API_KEY is not configured.");
                return null;
            }

            try{
                $payload = json_encode([
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

                $ch = curl_init(self::ENDPOINT . '?key=' . GEMINI_API_KEY);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if($response === false){
                    error_log("GeminiService: curl error - " . $curlError);
                    return null;
                }
                if($httpCode !== 200){
                    error_log("GeminiService: HTTP {$httpCode} - " . $response);
                    return null;
                }

                $data = json_decode($response, true);
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                return $text !== null ? trim($text) : null;
            }catch(Exception $e){
                error_log("GeminiService: " . $e->getMessage());
                return null;
            }
        }

        public static function generateInsight($metrics){
            return self::callApi(self::buildInsightPrompt($metrics));
        }

        public static function generateDashboardSummary($metrics){
            return self::callApi(self::buildSummaryPrompt($metrics));
        }
    }
