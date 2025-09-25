<?php
require_once __DIR__ . '/Model.php';

class SleepLog extends Model {
    
    /**
     * Create a new sleep log entry
     * @param int $userId User ID
     * @param float $hours Hours of sleep
     * @param string $logDate Date in YYYY-MM-DD format
     * @return bool Success status
     */
    public function createSleepLog(int $userId, float $hours, string $logDate): bool {
        try {
            $existingLog = $this->getSleepLogByDate($userId, $logDate);
            
            if ($existingLog) {
                return $this->updateSleepLog($userId, $hours, $logDate);
            }
            
            $sql = "INSERT INTO sleep_logs (user_id, hours, log_date) VALUES (:user_id, :hours, :log_date)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':hours', $hours);
            $stmt->bindParam(':log_date', $logDate);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating sleep log: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing sleep log
     * @param int $userId User ID
     * @param float $hours Hours of sleep
     * @param string $logDate Date in YYYY-MM-DD format
     * @return bool Success status
     */
    public function updateSleepLog(int $userId, float $hours, string $logDate): bool {
        try {
            $sql = "UPDATE sleep_logs SET hours = :hours WHERE user_id = :user_id AND log_date = :log_date";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':hours', $hours);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':log_date', $logDate);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating sleep log: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get sleep log by date for a specific user
     * @param int $userId User ID
     * @param string $logDate Date in YYYY-MM-DD format
     * @return array|null Sleep log data or null if not found
     */
    public function getSleepLogByDate(int $userId, string $logDate): ?array {
        try {
            $sql = "SELECT * FROM sleep_logs WHERE user_id = :user_id AND log_date = :log_date";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':log_date', $logDate);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error getting sleep log: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get sleep logs for a date range
     * @param int $userId User ID
     * @param string $startDate Start date in YYYY-MM-DD format
     * @param string $endDate End date in YYYY-MM-DD format
     * @return array Array of sleep logs
     */
    public function getSleepLogsForPeriod(int $userId, string $startDate, string $endDate): array {
        try {
            $sql = "SELECT * FROM sleep_logs 
                    WHERE user_id = :user_id 
                    AND log_date BETWEEN :start_date AND :end_date 
                    ORDER BY log_date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting sleep logs for period: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get sleep statistics for a user over a period
     * @param int $userId User ID
     * @param string $startDate Start date in YYYY-MM-DD format
     * @param string $endDate End date in YYYY-MM-DD format
     * @return array Statistics including average, min, max, total days logged
     */
    public function getSleepStatsForPeriod(int $userId, string $startDate, string $endDate): array {
        try {
            $sql = "SELECT 
                        COUNT(*) as days_logged,
                        AVG(hours) as average_hours,
                        MIN(hours) as min_hours,
                        MAX(hours) as max_hours,
                        SUM(hours) as total_hours
                    FROM sleep_logs 
                    WHERE user_id = :user_id 
                    AND log_date BETWEEN :start_date AND :end_date";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            
            $stats = $stmt->fetch();
            
            $totalDays = (new DateTime($endDate))->diff(new DateTime($startDate))->days + 1;
            
            return [
                'daysLogged' => (int)$stats['days_logged'],
                'totalDaysInPeriod' => $totalDays,
                'averageHours' => $stats['average_hours'] ? round((float)$stats['average_hours'], 2) : 0,
                'minHours' => (float)$stats['min_hours'] ?: 0,
                'maxHours' => (float)$stats['max_hours'] ?: 0,
                'totalHours' => (float)$stats['total_hours'] ?: 0,
                'complianceRate' => $totalDays > 0 ? round(((int)$stats['days_logged'] / $totalDays) * 100, 1) : 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting sleep stats: " . $e->getMessage());
            return [
                'daysLogged' => 0,
                'totalDaysInPeriod' => 0,
                'averageHours' => 0,
                'minHours' => 0,
                'maxHours' => 0,
                'totalHours' => 0,
                'complianceRate' => 0
            ];
        }
    }
    
    /**
     * Get sleep recommendation based on user's age and BMI
     * @param int $age User's age
     * @param float $bmi User's BMI (optional, affects recommendations)
     * @return array Sleep recommendation with hours range and tips
     */
    public function getSleepRecommendation(int $age, float $bmi = null): array {
        $recommendation = [
            'minHours' => 7,
            'maxHours' => 9,
            'optimalHours' => 8,
            'category' => 'Adult',
            'tips' => []
        ];
        
        if ($age >= 13 && $age <= 17) {
            $recommendation['minHours'] = 8;
            $recommendation['maxHours'] = 10;
            $recommendation['optimalHours'] = 9;
            $recommendation['category'] = 'Teenager';
            $recommendation['tips'][] = "Teenagers need more sleep for growth and development";
            $recommendation['tips'][] = "Try to maintain consistent sleep schedules even on weekends";
        } elseif ($age >= 18 && $age <= 25) {
            $recommendation['minHours'] = 7;
            $recommendation['maxHours'] = 9;
            $recommendation['optimalHours'] = 8;
            $recommendation['category'] = 'Young Adult';
            $recommendation['tips'][] = "Establish good sleep habits early in adulthood";
            $recommendation['tips'][] = "Limit screen time before bed";
        } elseif ($age >= 26 && $age <= 64) {
            $recommendation['minHours'] = 7;
            $recommendation['maxHours'] = 9;
            $recommendation['optimalHours'] = 8;
            $recommendation['category'] = 'Adult';
            $recommendation['tips'][] = "Maintain regular sleep-wake cycles";
            $recommendation['tips'][] = "Create a relaxing bedtime routine";
        } else {
            $recommendation['minHours'] = 7;
            $recommendation['maxHours'] = 8;
            $recommendation['optimalHours'] = 7.5;
            $recommendation['category'] = 'Older Adult';
            $recommendation['tips'][] = "Sleep quality becomes more important than quantity";
            $recommendation['tips'][] = "Avoid daytime naps longer than 30 minutes";
        }
        
        if ($bmi !== null) {
            if ($bmi < 18.5) {
                $recommendation['tips'][] = "Adequate sleep supports healthy weight gain";
                $recommendation['tips'][] = "Consider eating a light snack before bed";
            } elseif ($bmi >= 25 && $bmi < 30) {
                $recommendation['tips'][] = "Good sleep helps regulate hunger hormones";
                $recommendation['tips'][] = "Avoid late-night eating";
                $recommendation['optimalHours'] += 0.5;
            } elseif ($bmi >= 30) {
                $recommendation['tips'][] = "Quality sleep is crucial for weight management";
                $recommendation['tips'][] = "Consider sleep position adjustments for comfort";
                $recommendation['tips'][] = "Consult healthcare provider about sleep apnea risk";
                $recommendation['optimalHours'] += 0.5;
            }
        }
        
        $generalTips = [
            "Keep your bedroom cool, dark, and quiet",
            "Avoid caffeine 6 hours before bedtime",
            "Regular exercise improves sleep quality",
            "Limit alcohol consumption, especially before bed"
        ];
        
        $recommendation['tips'] = array_merge($recommendation['tips'], $generalTips);
        
        return $recommendation;
    }
    
    /**
     * Evaluate sleep quality based on recommended hours
     * @param float $actualHours Hours actually slept
     * @param int $age User's age
     * @param float $bmi User's BMI (optional)
     * @return array Evaluation with status, score, and feedback
     */
    public function evaluateSleepQuality(float $actualHours, int $age, float $bmi = null): array {
        $recommendation = $this->getSleepRecommendation($age, $bmi);
        
        $evaluation = [
            'status' => 'poor',
            'score' => 0,
            'feedback' => '',
            'color' => 'red',
            'recommendation' => $recommendation
        ];
        
        if ($actualHours >= $recommendation['minHours'] && $actualHours <= $recommendation['maxHours']) {
            $evaluation['status'] = 'excellent';
            $evaluation['score'] = 100;
            $evaluation['feedback'] = 'Great job! You\'re getting optimal sleep for your age group.';
            $evaluation['color'] = 'green';
        } elseif ($actualHours >= ($recommendation['minHours'] - 1) && $actualHours <= ($recommendation['maxHours'] + 1)) {
            $evaluation['status'] = 'good';
            $evaluation['score'] = 80;
            $evaluation['feedback'] = 'Good sleep duration! Try to aim for the optimal range.';
            $evaluation['color'] = 'yellow';
        } elseif ($actualHours < $recommendation['minHours']) {
            $deficit = $recommendation['minHours'] - $actualHours;
            $evaluation['status'] = 'insufficient';
            $evaluation['score'] = max(20, 60 - ($deficit * 15));
            $evaluation['feedback'] = "You need about " . round($deficit, 1) . " more hours of sleep.";
            $evaluation['color'] = 'red';
        } else {
            // Too much sleep
            $excess = $actualHours - $recommendation['maxHours'];
            $evaluation['status'] = 'excessive';
            $evaluation['score'] = max(30, 70 - ($excess * 10));
            $evaluation['feedback'] = "You might be sleeping too much. Consider reducing by " . round($excess, 1) . " hours.";
            $evaluation['color'] = 'orange';
        }
        
        return $evaluation;
    }
    
    /**
     * Delete a sleep log entry
     * @param int $userId User ID
     * @param string $logDate Date in YYYY-MM-DD format
     * @return bool Success status
     */
    public function deleteSleepLog(int $userId, string $logDate): bool {
        try {
            $sql = "DELETE FROM sleep_logs WHERE user_id = :user_id AND log_date = :log_date";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':log_date', $logDate);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting sleep log: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get recent sleep logs (last 7 days)
     * @param int $userId User ID
     * @return array Recent sleep logs
     */
    public function getRecentSleepLogs(int $userId): array {
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-7 days'));
        
        return $this->getSleepLogsForPeriod($userId, $startDate, $endDate);
    }

    /**
     * Get sleep hours for today for a specific user
     * @param int $userId User ID
     * @return float|null Hours of sleep for today or null if not logged
     */
    public function getSleepForTodayByUser(int $userId): ?float {
        $today = date('Y-m-d');
        $todaysLog = $this->getSleepLogByDate($userId, $today);
        
        return $todaysLog ? (float)$todaysLog['hours'] : null;
    }
}