<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/MoodLog.php';

class MoodLogController extends Controller
{
    /**
     * Handles the AJAX request to save a mood log for today.
     */
    public function log()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['mood'])) {
            http_response_code(400); 
            echo json_encode(['success' => false, 'message' => 'Mood is required.']);
            exit();
        }

        $userId = $_SESSION['user_id'];
        $mood = ucfirst(strtolower(trim($input['mood'])));
        $notes = $input['notes'] ?? null; 

        $moodLogModel = new MoodLog();
        $success = $moodLogModel->createOrUpdateForToday($userId, $mood, $notes);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500); 
            echo json_encode(['success' => false, 'message' => 'Failed to save mood log.']);
        }
    }
}