<?php
require_once __DIR__ . '/Model.php';

class Checklist extends Model {

    /**
     * Gets all active checklist items for a user, updates missed items, and organizes by type.
     * @param int $userId The ID of the logged-in user.
     * @return array An array containing 'morning' and 'night' checklists.
     */
    public function getItemsForToday(int $userId): array {
        $this->updateMissedItems($userId);
        
        $sql = "
            SELECT 
                ci.id, 
                ci.item_text AS task, 
                ci.type,
                ci.due_date,
                ci.completion_status,
                ci.completed_at,
                TIME(ci.due_date) as due_time,
                DATE(ci.due_date) as due_date_only,
                CASE 
                    WHEN ci.completion_status = 'completed' THEN 1
                    ELSE 0
                END AS done
            FROM 
                checklist_items ci
            WHERE 
                ci.user_id = :userId AND ci.is_active = TRUE
            ORDER BY 
                ci.due_date ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $allItems = $stmt->fetchAll();

        $sortedLists = [
            'morning' => [],
            'night' => []
        ];

        foreach ($allItems as $item) {
            $item['done'] = (bool)$item['done']; 
            
            $item['is_missed'] = $item['completion_status'] === 'missed';
            $item['is_completed'] = $item['completion_status'] === 'completed';
            $item['is_pending'] = $item['completion_status'] === 'pending';
            
            if ($item['type'] === 'morning') {
                $sortedLists['morning'][] = $item;
            } else {
                $sortedLists['night'][] = $item;
            }
        }

        return $sortedLists;
    }

    /**
     * Updates items that have missed their due date.
     * @param int $userId The ID of the logged-in user.
     */
    private function updateMissedItems(int $userId): void {
        $sql = "
            UPDATE checklist_items 
            SET completion_status = 'missed'
            WHERE user_id = :userId 
                AND completion_status = 'pending' 
                AND due_date < NOW()
                AND is_active = TRUE
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Determines checklist type based on the time component of due_date.
     * @param string $time Time in HH:MM:SS or HH:MM format
     * @return string 'morning' if time is before 17:00, 'night' otherwise
     */
    public function determineTypeFromTime(string $time): string {
        $hour = (int)substr($time, 0, 2);
        return $hour < 17 ? 'morning' : 'night';
    }

    /**
     * Sets the completion status for a specific item.
     * @param int $userId The ID of the logged-in user.
     * @param int $itemId The ID of the checklist item.
     * @param bool $isCompleted The new status (true for completed, false for pending).
     * @return bool True on success, false on failure.
     */
    public function setCompletionStatus(int $userId, int $itemId, bool $isCompleted): bool {
        if ($isCompleted) {
            $sql = "
                UPDATE checklist_items 
                SET completion_status = 'completed', completed_at = NOW() 
                WHERE id = :itemId AND user_id = :userId
            ";
        } else {
            $sql = "
                UPDATE checklist_items 
                SET completion_status = CASE 
                    WHEN due_date < NOW() THEN 'missed'
                    ELSE 'pending'
                END,
                completed_at = NULL
                WHERE id = :itemId AND user_id = :userId
            ";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':itemId', $itemId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Adds a new checklist item for a user.
     * @param int $userId The ID of the logged-in user.
     * @param string $taskText The description of the task.
     * @param string $dueDate The due date and time in YYYY-MM-DD HH:MM:SS format.
     * @return int|false The ID of the newly inserted item, or false on failure.
     */
    public function add(int $userId, string $taskText, string $dueDate) {
        $time = date('H:i:s', strtotime($dueDate));
        $type = $this->determineTypeFromTime($time);

        $currentTime = new DateTime();
        $dueDateTime = new DateTime($dueDate);
        $initialStatus = ($dueDateTime < $currentTime) ? 'missed' : 'pending';

        $sql = "
            INSERT INTO checklist_items (user_id, item_text, type, due_date, completion_status) 
            VALUES (:userId, :taskText, :type, :dueDate, :status)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':taskText', $taskText, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':dueDate', $dueDate, PDO::PARAM_STR);
        $stmt->bindParam(':status', $initialStatus, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }
    
    /**
     * Updates an existing checklist item.
     * @param int $userId The ID of the logged-in user.
     * @param int $itemId The ID of the item to update.
     * @param string $taskText The new description.
     * @param string $dueDate The new due date and time in YYYY-MM-DD HH:MM:SS format.
     * @return bool True on success, false on failure.
     */
    public function update(int $userId, int $itemId, string $taskText, string $dueDate): bool {
        $time = date('H:i:s', strtotime($dueDate));
        $type = $this->determineTypeFromTime($time);
        
        $currentTime = new DateTime();
        $dueDateTime = new DateTime($dueDate);
        
        $sql = "
            UPDATE checklist_items 
            SET item_text = :taskText, 
                type = :type, 
                due_date = :dueDate,
                completion_status = CASE
                    WHEN completion_status = 'completed' THEN 'completed'
                    WHEN :dueDateTime < :currentTime THEN 'missed'
                    ELSE 'pending'
                END
            WHERE id = :itemId AND user_id = :userId
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':taskText', $taskText);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':dueDate', $dueDate);
        $stmt->bindParam(':dueDateTime', $dueDate);
        $stmt->bindParam(':currentTime', $currentTime->format('Y-m-d H:i:s'));
        $stmt->bindParam(':itemId', $itemId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Deletes a checklist item.
     * @param int $userId The ID of the logged-in user.
     * @param int $itemId The ID of the item to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $userId, int $itemId): bool {
        $sql = "DELETE FROM checklist_items WHERE id = :itemId AND user_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':itemId', $itemId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Gets statistics for a user's checklist performance over a period.
     * @param int $userId The ID of the logged-in user.
     * @param string $startDate Start date in YYYY-MM-DD format.
     * @param string $endDate End date in YYYY-MM-DD format.
     * @return array Statistics array with completion rates and counts.
     */
    public function getStatsForPeriod(int $userId, string $startDate, string $endDate): array {
        $this->updateMissedItems($userId);
        
        $totalDays = (new DateTime($endDate))->diff(new DateTime($startDate))->days + 1;
        
        $sql = "
            SELECT 
                ci.type,
                ci.completion_status,
                COUNT(*) as count
            FROM checklist_items ci
            WHERE ci.user_id = :userId 
                AND ci.is_active = TRUE
                AND DATE(ci.due_date) BETWEEN :startDate AND :endDate
            GROUP BY ci.type, ci.completion_status
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats = [
            'morning' => ['completed' => 0, 'pending' => 0, 'missed' => 0, 'total' => 0],
            'night' => ['completed' => 0, 'pending' => 0, 'missed' => 0, 'total' => 0],
            'overall' => ['completed' => 0, 'pending' => 0, 'missed' => 0, 'total' => 0]
        ];
        
        foreach ($results as $row) {
            $type = $row['type'];
            $status = $row['completion_status'];
            $count = (int)$row['count'];
            
            $stats[$type][$status] = $count;
            $stats[$type]['total'] += $count;
            $stats['overall'][$status] += $count;
            $stats['overall']['total'] += $count;
        }
        
        $morningRate = $stats['morning']['total'] > 0 ? 
            round(($stats['morning']['completed'] / $stats['morning']['total']) * 100, 1) : 0;
        $nightRate = $stats['night']['total'] > 0 ? 
            round(($stats['night']['completed'] / $stats['night']['total']) * 100, 1) : 0;
        $overallRate = $stats['overall']['total'] > 0 ? 
            round(($stats['overall']['completed'] / $stats['overall']['total']) * 100, 1) : 0;
        
        return [
            'morningRate' => $morningRate,
            'nightRate' => $nightRate,
            'overallRate' => $overallRate,
            'completed' => $stats['overall']['completed'],
            'pending' => $stats['overall']['pending'],
            'missed' => $stats['overall']['missed'],
            'total' => $stats['overall']['total'],
            'morningStats' => $stats['morning'],
            'nightStats' => $stats['night'],
            'totalDays' => $totalDays,
            'completionRate' => $overallRate,
            'totalItems' => $stats['morning']['total'] + $stats['night']['total'],
            'successRate' => $stats['overall']['total'] > 0 ? 
                round((($stats['overall']['completed']) / $stats['overall']['total']) * 100, 1) : 0,
            'missedRate' => $stats['overall']['total'] > 0 ? 
                round(($stats['overall']['missed'] / $stats['overall']['total']) * 100, 1) : 0,
            'pendingRate' => $stats['overall']['total'] > 0 ? 
                round(($stats['overall']['pending'] / $stats['overall']['total']) * 100, 1) : 0,
            'period' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'days' => $totalDays
            ]
        ];
    }

    /**
     * Gets items by completion status.
     * @param int $userId The ID of the logged-in user.
     * @param string $status The completion status ('pending', 'completed', 'missed').
     * @param int|null $limit Optional limit for results.
     * @return array Array of checklist items.
     */
    public function getItemsByStatus(int $userId, string $status, ?int $limit = null): array {
        $this->updateMissedItems($userId);
        
        $sql = "
            SELECT 
                ci.id, 
                ci.item_text AS task, 
                ci.type,
                ci.due_date,
                ci.completion_status,
                ci.completed_at
            FROM checklist_items ci
            WHERE ci.user_id = :userId 
                AND ci.completion_status = :status 
                AND ci.is_active = TRUE
            ORDER BY ci.due_date ASC
        ";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}