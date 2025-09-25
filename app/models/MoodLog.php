<?php
require_once __DIR__ . '/Model.php';

class MoodLog extends Model {

    /**
     * Creates a new mood log for today, or updates an existing one for today.
     * @param int $userId The ID of the logged-in user.
     * @param string $mood The mood string (e.g., 'Joy', 'Sad').
     * @param string|null $notes Optional notes for the log.
     * @return bool True on success, false on failure.
     */
    public function createOrUpdateForToday(int $userId, string $mood, ?string $notes = null): bool {
        $sql = "
            INSERT INTO mood_logs (user_id, log_date, mood, notes)
            VALUES (:userId, CURDATE(), :mood, :notes)
            ON DUPLICATE KEY UPDATE 
                mood = VALUES(mood), 
                notes = VALUES(notes)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':mood', $mood, PDO::PARAM_STR);
        $stmt->bindParam(':notes', $notes, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Fetches moods for the last 7 days for a user, filling in blanks for days with no log.
     * The results are ordered from most recent (today) to oldest.
     * @param int $userId The ID of the logged-in user.
     * @param int $limit The number of past days to show.
     * @return array An array of 7 mood log structures.
     */
    public function getLatestByUser(int $userId, int $limit = 7): array {
        $startDate = date('Y-m-d', strtotime("-".($limit-1)." days"));
        $endDate = date('Y-m-d');
        
        $stmt = $this->db->prepare(
            "SELECT mood, log_date 
            FROM mood_logs 
            WHERE user_id = :userId AND log_date BETWEEN :startDate AND :endDate"
        );
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        
        $logsByDate = [];
        foreach ($stmt->fetchAll() as $log) {
            $logsByDate[$log['log_date']] = $log['mood'];
        }

        $sevenDayMoods = [];
        for ($i = 0; $i < $limit; $i++) {
            $currentDate = date('Y-m-d', strtotime("-$i days"));
            $dayOfMonth = date('j', strtotime($currentDate)); // 'j' gives the day without leading zeros

            if (isset($logsByDate[$currentDate])) {
                $sevenDayMoods[] = [
                    'day' => $dayOfMonth,
                    'mood' => $logsByDate[$currentDate],
                    'tracked' => true
                ];
            } else {
                $sevenDayMoods[] = [
                    'day' => $dayOfMonth,
                    'mood' => 'untracked', 
                    'tracked' => false
                ];
            }
        }

        return $sevenDayMoods;
    }


    /**
     * Counts the total number of logs (reports) for a user for today.
     * @param int $userId The ID of the logged-in user.
     * @return int The count of reports.
     */
    public function countForTodayByUser(int $userId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) 
             FROM mood_logs 
             WHERE user_id = :userId AND log_date = CURDATE()"
        );
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getStatsForPeriod(int $userId, string $startDate, string $endDate): array {
        $stmt = $this->db->prepare(
            "SELECT mood, COUNT(*) as count 
            FROM mood_logs 
            WHERE user_id = :userId AND log_date BETWEEN :startDate AND :endDate 
            GROUP BY mood"
        );
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        $summary = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 

        $total = array_sum($summary);
        if ($total > 0) {
            foreach ($summary as $mood => $count) {
                $summary[$mood] = round(($count / $total) * 100);
            }
        }
        
        $averageMood = 'N/A';
        if (!empty($summary)) {
            arsort($summary);
            $averageMood = key($summary);
        }
        
        return [
            'average' => $averageMood,
            'summary' => $summary,
            'trend' => [] 
        ];
    }
    
    /**
     * This counts from the first mood log date to today, regardless of consecutive logging
     * @param int $userId The ID of the logged-in user.
     * @return int The count of days since first log.
     */
    public function getConsecutiveDayCount(int $userId): int {
        $stmt = $this->db->prepare(
            "SELECT MIN(log_date) as first_date 
             FROM mood_logs 
             WHERE user_id = :userId"
        );
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        $firstDate = $result['first_date'] ?? null;
        
        if (!$firstDate) {
            return 0; 
        }
        
        $firstDateTime = new DateTime($firstDate);
        $todayDateTime = new DateTime(date('Y-m-d'));
        $daysDiff = $firstDateTime->diff($todayDateTime)->days;
        
        return $daysDiff + 1;
    }
}