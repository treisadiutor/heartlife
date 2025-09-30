<?php
require_once __DIR__ . '/Model.php';

class MentalHealthAssessment extends Model {
    /**
     * Saves a mental health assessment for a user.
     * @param int $userId The ID of the logged-in user.
     * @param array $answers Array of answers for each section
     * @param array $scores Array of scores for each section
     * @param string $feedback Overall feedback based on the assessment
     * @return int|false The ID of the newly inserted assessment, or false on failure
     */
    public function save(int $userId, array $answers, array $scores, string $feedback) {
        try {
            $this->db->beginTransaction();

            // First delete any existing assessment for today to avoid duplicates
            $sql = "DELETE FROM mental_health_assessments 
                   WHERE user_id = :userId AND DATE(created_at) = CURRENT_DATE()";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            // Insert the new assessment
            $sql = "INSERT INTO mental_health_assessments (
                    user_id, date, sleep_rest_score, body_energy_score,
                    emotions_mood_score, social_support_score, mind_focus_score,
                    self_care_score, red_flags_score, total_score, feedback
                ) VALUES (
                    :userId, CURRENT_DATE(), :sleepScore, :bodyScore,
                    :emotionsScore, :socialScore, :mindScore,
                    :selfCareScore, :redFlagsScore, :totalScore, :feedback
                )";

            $stmt = $this->db->prepare($sql);
            
            // Bind main assessment parameters directly
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':sleepScore', $scores['sleep_rest'], PDO::PARAM_INT);
            $stmt->bindValue(':bodyScore', $scores['body_energy'], PDO::PARAM_INT);
            $stmt->bindValue(':emotionsScore', $scores['emotions_mood'], PDO::PARAM_INT);
            $stmt->bindValue(':socialScore', $scores['social_support'], PDO::PARAM_INT);
            $stmt->bindValue(':mindScore', $scores['mind_focus'], PDO::PARAM_INT);
            $stmt->bindValue(':selfCareScore', $scores['self_care'], PDO::PARAM_INT);
            $stmt->bindValue(':redFlagsScore', $scores['red_flags'], PDO::PARAM_INT);
            $stmt->bindValue(':totalScore', $scores['total'], PDO::PARAM_INT);
            $stmt->bindValue(':feedback', $feedback, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert assessment record");
            }
            
            $assessmentId = $this->db->lastInsertId();
            if (!$assessmentId) {
                throw new Exception("Failed to get last insert ID");
            }

            // Prepare statement for answers
            $sql = "INSERT INTO assessment_answers (
                    assessment_id, section_name, question_number, answer
                ) VALUES (
                    :assessmentId, :sectionName, :questionNumber, :answer
                )";
            
            $stmt = $this->db->prepare($sql);
            
            // Insert answers
            foreach ($answers as $section => $questions) {
                foreach ($questions as $questionNum => $answer) {
                    $stmt->bindValue(':assessmentId', $assessmentId, PDO::PARAM_INT);
                    $stmt->bindValue(':sectionName', $section, PDO::PARAM_STR);
                    $stmt->bindValue(':questionNumber', $questionNum, PDO::PARAM_INT);
                    $stmt->bindValue(':answer', $answer ? 1 : 0, PDO::PARAM_INT);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to insert answer record for section $section, question $questionNum");
                    }
                }
            }

            $this->db->commit();
            return $assessmentId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error saving mental health assessment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gets the latest mental health assessment for a user.
     * @param int $userId The ID of the logged-in user.
     * @return array|null The assessment data or null if none exists
     */
    public function getLatest(int $userId): ?array {
        $sql = "SELECT * FROM mental_health_assessments
               WHERE user_id = :userId
               ORDER BY date DESC, created_at DESC
               LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $assessment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$assessment) {
            return null;
        }

        $sql = "SELECT section_name, question_number, answer
               FROM assessment_answers
               WHERE assessment_id = :assessmentId
               ORDER BY section_name, question_number";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':assessmentId', $assessment['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $organizedAnswers = [];
        foreach ($answers as $answer) {
            $organizedAnswers[$answer['section_name']][$answer['question_number']] = (bool)$answer['answer'];
        }
        
        $assessment['answers'] = $organizedAnswers;
        return $assessment;
    }

    /**
     * Gets the mental health assessment history for a user within a date range.
     * @param int $userId The ID of the logged-in user.
     * @param string $startDate Start date in YYYY-MM-DD format
     * @param string $endDate End date in YYYY-MM-DD format
     * @return array Array of assessment records
     */
    public function getHistory(int $userId, string $startDate, string $endDate): array {
        $sql = "SELECT * FROM mental_health_assessments
               WHERE user_id = :userId
               AND date BETWEEN :startDate AND :endDate
               ORDER BY date DESC, created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate section scores based on answers
     * @param array $answers Array of answers for a section
     * @return int Score for the section (0-100)
     */
    public static function calculateSectionScore(array $answers): int {
        if (empty($answers)) {
            return 0;
        }

        $totalAnswers = count($answers);
        $positiveAnswers = count(array_filter($answers));
        
        return round(($positiveAnswers / $totalAnswers) * 100);
    }

    /**
     * Generate feedback based on scores
     * @param array $scores Array of scores for each section
     * @return string Appropriate feedback message
     */
    public static function generateFeedback(array $scores): string {
        $totalScore = array_sum($scores) / count($scores);
        $redFlagsScore = $scores['red_flags'];

        if ($redFlagsScore > 60) {
            return "🚨 We noticed some red flags in your responses. It might be helpful to reach out to a mental health professional or trusted person for support.";
        }
        
        if ($totalScore >= 80) {
            return "✅ Great job! You're taking excellent care of your mental health today. Keep up these positive habits!";
        }
        
        if ($totalScore >= 60) {
            $lowScoreSections = array_filter($scores, fn($score) => $score < 60);
            $areas = array_keys($lowScoreSections);
            
            if (!empty($areas)) {
                $areasStr = implode(', ', array_map(fn($area) => str_replace('_', ' ', $area), $areas));
                return "⚠️ You're doing okay overall, but might want to focus more on: {$areasStr}.";
            }
            
            return "⚠️ You're doing okay overall, but there's room for improvement in some areas.";
        }
        
        return "⚠️ Today seems challenging. Consider focusing on basic self-care and reaching out for support if needed.";
    }
}