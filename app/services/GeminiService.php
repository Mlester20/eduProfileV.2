<?php

    /**
     * Calls the Gemini API to turn computed at-risk metrics into a short
     * narrative insight. Only ever receives aggregate counts (grade level,
     * section, failing/absence/disciplinary counts) — never the student's
     * name — so no personally-identifiable data leaves the server.
     */

    class GeminiService{
        const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

        private static function buildPrompt($metrics){
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

        public static function generateInsight($metrics){
            $secretsFile = __DIR__ . '/../../database/config/secrets.php';
            if(!defined('GEMINI_API_KEY') && file_exists($secretsFile)){
                require_once $secretsFile;
            }
            if(!defined('GEMINI_API_KEY') || GEMINI_API_KEY === '' || GEMINI_API_KEY === 'PASTE_YOUR_KEY_HERE'){
                error_log("GeminiService: GEMINI_API_KEY is not configured.");
                return null;
            }

            try{
                $prompt = self::buildPrompt($metrics);
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
    }
