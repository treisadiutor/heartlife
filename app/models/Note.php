<?php
require_once __DIR__ . '/Model.php';

class Note extends Model {

    /**
     * Gets all notes for a specific user, categorized by status.
     * @param int $userId The ID of the logged-in user.
     * @return array An array containing 'recent', 'completed', and 'pinned' notes.
     */
    public function getAllByUser(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT id, title, content, status, DATE_FORMAT(created_at, '%b %d, %Y') as date 
             FROM notes 
             WHERE user_id = :userId 
             ORDER BY updated_at DESC"
        );
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $allNotes = $stmt->fetchAll();

        $categorizedNotes = [
            'recent' => [],
            'completed' => [],
            'pinned' => []
        ];

        foreach ($allNotes as $note) {
            $note['excerpt'] = substr($note['content'], 0, 100) . (strlen($note['content']) > 100 ? '...' : '');

            switch ($note['status']) {
                case 'active':
                    $categorizedNotes['recent'][] = $note;
                    break;
                case 'completed':
                    $categorizedNotes['completed'][] = $note;
                    break;
                case 'pinned':
                    $categorizedNotes['pinned'][] = $note;
                    break;
            }
        }
        return $categorizedNotes;
    }

    /**
     * Creates a new note for a user.
     * @param int $userId
     * @param string $title
     * @param string $content
     * @return bool True on success, false on failure.
     */
    public function create(int $userId, string $title, string $content): bool {
        $sql = "INSERT INTO notes (user_id, title, content) VALUES (:userId, :title, :content)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        return $stmt->execute();
    }

    /**
     * Updates the status of one or more notes for a user.
     * @param int $userId
     * @param array $noteIds An array of note IDs to update.
     * @param string $newStatus The new status ('active', 'completed', 'pinned').
     * @return bool True on success, false on failure.
     */
    public function updateStatus(int $userId, array $noteIds, string $newStatus): bool {
        if (empty($noteIds) || !in_array($newStatus, ['active', 'completed', 'pinned'])) {
            return false;
        }
        
        $placeholders = implode(',', array_fill(0, count($noteIds), '?'));
        
        $sql = "UPDATE notes SET status = ? WHERE user_id = ? AND id IN ($placeholders)";
        $params = array_merge([$newStatus, $userId], $noteIds);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Deletes one or more notes for a user.
     * @param int $userId
     * @param array $noteIds An array of note IDs to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $userId, array $noteIds): bool {
        if (empty($noteIds)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($noteIds), '?'));
        $sql = "DELETE FROM notes WHERE user_id = ? AND id IN ($placeholders)";
        $params = array_merge([$userId], $noteIds);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Gets note statistics for a specific period.
     * @param int $userId The ID of the logged-in user.
     * @param string $startDate Start date in Y-m-d format.
     * @param string $endDate End date in Y-m-d format.
     * @return array Statistics about notes in the period.
     */
    public function getStatsForPeriod(int $userId, string $startDate, string $endDate): array {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count, status 
             FROM notes 
             WHERE user_id = :userId 
             AND DATE(created_at) >= :startDate 
             AND DATE(created_at) <= :endDate
             GROUP BY status"
        );
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        $statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $totalInPeriod = array_sum($statusCounts);
        
        $totalStmt = $this->db->prepare(
            "SELECT COUNT(*) as total_count, 
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as total_active,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as total_completed,
                    SUM(CASE WHEN status = 'pinned' THEN 1 ELSE 0 END) as total_pinned
             FROM notes 
             WHERE user_id = :userId"
        );
        $totalStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $totalStmt->execute();
        $totals = $totalStmt->fetch();
        
        $totalAllTime = (int)$totals['total_count'];
        
        if ($totalInPeriod == 0 && $totalAllTime > 0) {
            return [
                'count' => $totalAllTime,
                'statusBreakdown' => [
                    'active' => (int)$totals['total_active'],
                    'completed' => (int)$totals['total_completed'],
                    'pinned' => (int)$totals['total_pinned']
                ],
                'active' => (int)$totals['total_active'],
                'completed' => (int)$totals['total_completed'],
                'pinned' => (int)$totals['total_pinned'],
                'inPeriod' => false,
                'periodStart' => $startDate,
                'periodEnd' => $endDate
            ];
        }
        
        if ($totalAllTime == 0) {
            return [
                'count' => 0,
                'statusBreakdown' => [],
                'active' => 0,
                'completed' => 0,
                'pinned' => 0,
                'inPeriod' => false,
                'periodStart' => $startDate,
                'periodEnd' => $endDate
            ];
        }
        
        return [
            'count' => $totalInPeriod,
            'totalAllTime' => $totalAllTime,
            'statusBreakdown' => $statusCounts,
            'active' => (int)($statusCounts['active'] ?? 0),
            'completed' => (int)($statusCounts['completed'] ?? 0),
            'pinned' => (int)($statusCounts['pinned'] ?? 0),
            'inPeriod' => true,
            'periodStart' => $startDate,
            'periodEnd' => $endDate
        ];
    }
}