<?php
require_once __DIR__ . '/Model.php';

class BmiLog extends Model {
    public function getStatsForPeriod(int $userId, string $startDate, string $endDate): array {
        // Get logs within the period
        $stmt = $this->db->prepare(
            "SELECT bmi_value, log_date FROM bmi_logs 
             WHERE user_id = :userId AND log_date BETWEEN :startDate AND :endDate 
             ORDER BY log_date ASC"
        );
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        $logs = $stmt->fetchAll();

        // If no logs in period, get the latest BMI record overall
        $latestBmi = 0;
        $latestDate = null;
        
        if (empty($logs)) {
            $latestStmt = $this->db->prepare(
                "SELECT bmi_value, log_date FROM bmi_logs 
                 WHERE user_id = :userId 
                 ORDER BY log_date DESC 
                 LIMIT 1"
            );
            $latestStmt->bindParam(':userId', $userId);
            $latestStmt->execute();
            $latestLog = $latestStmt->fetch();
            
            if ($latestLog) {
                $latestBmi = $latestLog['bmi_value'];
                $latestDate = $latestLog['log_date'];
            }
        } else {
            $latestLog = end($logs);
            $latestBmi = $latestLog['bmi_value'];
            $latestDate = $latestLog['log_date'];
        }
        
        $class = 'N/A';
        if ($latestBmi > 0) {
            if ($latestBmi < 18.5) $class = 'Underweight';
            elseif ($latestBmi < 25) $class = 'Normal';
            elseif ($latestBmi < 30) $class = 'Overweight';
            else $class = 'Obese';
        }

        return [
            'progress' => array_column($logs, 'bmi_value'),
            'latest' => [
                'value' => $latestBmi, 
                'class' => $class,
                'date' => $latestDate,
                'inPeriod' => !empty($logs)
            ]
        ];
    }

    /**
     * Gets all BMI logs for a user.
     * @param int $userId
     * @return array
     */
    public function getAllByUserId(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM bmi_logs WHERE user_id = :userId ORDER BY log_date DESC"
        );
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Creates a new BMI log entry.
     * @param int $userId
     * @param float $height Height in cm
     * @param float $weight Weight in kg
     * @param float $bmiValue Calculated BMI value
     * @param string $logDate Date in Y-m-d format
     * @return bool
     */
    public function create(int $userId, float $height, float $weight, float $bmiValue, string $logDate): bool {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO bmi_logs (user_id, height_cm, weight_kg, bmi_value, log_date) 
                 VALUES (:userId, :height, :weight, :bmiValue, :logDate)"
            );
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':height', $height);
            $stmt->bindParam(':weight', $weight);
            $stmt->bindParam(':bmiValue', $bmiValue);
            $stmt->bindParam(':logDate', $logDate);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Updates a BMI log entry.
     * @param int $logId
     * @param float $height
     * @param float $weight
     * @param float $bmiValue
     * @param string $logDate
     * @return bool
     */
    public function update(int $logId, float $height, float $weight, float $bmiValue, string $logDate): bool {
        try {
            $stmt = $this->db->prepare(
                "UPDATE bmi_logs SET height_cm = :height, weight_kg = :weight, 
                 bmi_value = :bmiValue, log_date = :logDate WHERE id = :id"
            );
            $stmt->bindParam(':height', $height);
            $stmt->bindParam(':weight', $weight);
            $stmt->bindParam(':bmiValue', $bmiValue);
            $stmt->bindParam(':logDate', $logDate);
            $stmt->bindParam(':id', $logId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Deletes a BMI log entry.
     * @param int $logId
     * @param int $userId User ID for security check
     * @return bool
     */
    public function delete(int $logId, int $userId): bool {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM bmi_logs WHERE id = :id AND user_id = :userId"
            );
            $stmt->bindParam(':id', $logId);
            $stmt->bindParam(':userId', $userId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Gets a single BMI log entry by ID and user ID.
     * @param int $logId
     * @param int $userId
     * @return array|false
     */
    public function getById(int $logId, int $userId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM bmi_logs WHERE id = :id AND user_id = :userId"
        );
        $stmt->bindParam(':id', $logId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Gets the latest BMI log entry for a user.
     * @param int $userId
     * @return array|null Latest BMI log or null if none found
     */
    public function getLatestBmi(int $userId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM bmi_logs 
             WHERE user_id = :userId 
             ORDER BY log_date DESC, created_at DESC 
             LIMIT 1"
        );
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
}