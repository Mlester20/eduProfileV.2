<?php
require_once __DIR__ . '/../../core/Model.php';

    class DashboardSummaryModel extends Model{

        public function getSummary($schoolYearId){
            try{
                $query = "SELECT summary_text, generated_at FROM dashboard_ai_summaries WHERE school_year_id = ? LIMIT 1";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $schoolYearId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch(Exception $e){
                error_log("Error fetching dashboard AI summary: " . $e->getMessage());
                return null;
            }
        }

        public function saveSummary($schoolYearId, $text){
            try{
                $query = "INSERT INTO dashboard_ai_summaries (school_year_id, summary_text) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE summary_text = VALUES(summary_text), generated_at = CURRENT_TIMESTAMP";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("is", $schoolYearId, $text);
                $stmt->execute();
                return true;
            }catch(Exception $e){
                error_log("Error saving dashboard AI summary: " . $e->getMessage());
                return false;
            }
        }
    }
