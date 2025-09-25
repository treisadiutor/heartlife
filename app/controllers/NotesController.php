<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/User.php'; 
require_once __DIR__ . '/../models/MoodLog.php';

class NotesController extends Controller
{
    /**
     * Displays the main notes page.
     */
    public function notes()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $userEmail = $_SESSION['user_email'];

        $userModel = new User();
        
        $profilePicture = $_SESSION['profile_pic'] ?? null;
        
        if (!$profilePicture) {
            $user = $userModel->findById($userId);
            if ($user && !empty($user['profile_pic'])) {
                $profilePicture = $user['profile_pic'];
                $_SESSION['profile_pic'] = $profilePicture;
            }
        }
        
        $profilePicUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=4a5588&color=fffff0&size=128";
        
        if ($profilePicture && file_exists(__DIR__ . '/../../' . $profilePicture)) {
            $profilePicUrl = BASE_URL . '/' . $profilePicture;
        }

        $noteModel = new Note();
        $notes = $noteModel->getAllByUser($userId);
        
        $moodLogModel = new MoodLog();
        $dayCount = $moodLogModel->getConsecutiveDayCount($userId);

        $data = [
            'userEmail' => $userEmail,
            'username' => $username,
            'profilePicUrl' => $profilePicUrl,
            'dayCount' => $dayCount,
            'recentNotes' => $notes['recent'],
            'completedNotes' => $notes['completed'],
            'pinnedNotes' => $notes['pinned'],
        ];
        
        $this->view('notes/notes', $data);
    }

    /**
     * Handles creation of a new note.
     */
    public function create()
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/notes');
            exit();
        }
        
        $noteModel = new Note();
        $success = $noteModel->create($_SESSION['user_id'], $_POST['title'], $_POST['content']);
        
        header('Location: ' . BASE_URL . '/notes');
        exit();
    }

    /**
     * Handles AJAX requests to update note statuses (complete, pin, un-pin).
     */
    public function updateStatus()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['noteIds']) || empty($input['status'])) {
            http_response_code(400); exit();
        }
        
        $noteModel = new Note();
        $success = $noteModel->updateStatus($_SESSION['user_id'], $input['noteIds'], $input['status']);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    /**
     * Handles AJAX requests to delete notes.
     */
    public function delete()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['noteIds'])) {
            http_response_code(400); exit();
        }
        
        $noteModel = new Note();
        $success = $noteModel->delete($_SESSION['user_id'], $input['noteIds']);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }
}