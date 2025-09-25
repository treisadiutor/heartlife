<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Checklist.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/MoodLog.php';
class CareController extends Controller
{
    /**
     * Displays the self-care checklist page.
     */
    public function selfcare()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['user_email'];
        $username = $_SESSION['username'];

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

        $moodLogModel = new MoodLog();
        $dayCount = $moodLogModel->getConsecutiveDayCount($userId);


        $checklistModel = new Checklist();
        
        $checklists = $checklistModel->getItemsForToday($userId);
        
        $data = [
            'userEmail' => $userEmail,
            'username' => $username,
            'dayCount' => $dayCount,
            'profilePicUrl' => $profilePicUrl,
            'morningChecklist' => $checklists['morning'],
            'nightChecklist' => $checklists['night']
        ];
        
        $this->view('care/index', $data);
    }
    
    /**
     * Handles the AJAX request to toggle a checklist item's completion status.
     */
    public function toggleCompletion()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['itemId']) || !isset($input['isChecked'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit();
        }

        $userId = $_SESSION['user_id'];
        $itemId = (int)$input['itemId'];
        $isChecked = (bool)$input['isChecked'];
        
        $checklistModel = new Checklist();
        $success = $checklistModel->setCompletionStatus($userId, $itemId, $isChecked);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500); 
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }
    }
    /**
     * Handles the AJAX request to add a new checklist item.
     */
    public function add()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        if (empty($_POST['task_text']) || empty($_POST['due_date'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit();
        }

        $userId = $_SESSION['user_id'];
        $taskText = trim($_POST['task_text']);
        $dueDate = $_POST['due_date'];
        
        $checklistModel = new Checklist();
        $newId = $checklistModel->add($userId, $taskText, $dueDate);

        header('Content-Type: application/json');
        if ($newId) {
            echo json_encode([
                'success' => true,
                'newItem' => [
                    'id' => $newId,
                    'task' => $taskText,
                    'due_date' => $dueDate,
                    'type' => $checklistModel->determineTypeFromTime(date('H:i:s', strtotime($dueDate)))
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database insert failed']);
        }
    }

    public function update()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); exit();
        }
        
        if (empty($_POST['item_id']) || empty($_POST['task_text']) || empty($_POST['due_date'])) {
            http_response_code(400); exit();
        }

        $userId = $_SESSION['user_id'];
        $itemId = (int)$_POST['item_id'];
        $taskText = trim($_POST['task_text']);
        $dueDate = $_POST['due_date'];
        
        $checklistModel = new Checklist();
        $success = $checklistModel->update($userId, $itemId, $taskText, $dueDate);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    public function delete()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['itemId'])) {
            http_response_code(400); exit();
        }

        $userId = $_SESSION['user_id'];
        $itemId = (int)$input['itemId'];
        
        $checklistModel = new Checklist();
        $success = $checklistModel->delete($userId, $itemId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }
}