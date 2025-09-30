<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Checklist.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/MoodLog.php';
require_once __DIR__ . '/../models/MentalHealthAssessment.php';
class CareController extends Controller
{
    private $assessmentSections = [
        'sleep_rest' => [
            1 => 'Did you get at least 7–8 hours of sleep last night?',
            2 => 'Did you fall asleep easily without much trouble?',
            3 => 'Did you wake up feeling refreshed today?',
            4 => 'Did you avoid using gadgets 30 minutes before sleeping?',
            5 => 'Did you take short breaks or naps when feeling tired?'
        ],
        'body_energy' => [
            1 => 'Do you feel physically energized today?',
            2 => 'Did you eat balanced meals (with fruits, veggies, and protein)?',
            3 => 'Did you avoid skipping meals today?',
            4 => 'Did you engage in physical activity or exercise?',
            5 => 'Did you stay hydrated with enough water?'
        ],
        'emotions_mood' => [
            1 => 'Did you feel calm and positive most of the day?',
            2 => 'Did you avoid feeling overwhelmed or irritable today?',
            3 => 'Did you express your feelings in a healthy way?',
            4 => 'Did you do at least one activity that made you happy?',
            5 => 'Did you manage stress without overreacting?'
        ],
        'social_support' => [
            1 => 'Did you talk or connect with a friend, family, or peer today?',
            2 => 'Did you feel supported by people around you?',
            3 => 'Did you reach out to someone when you needed help?',
            4 => 'Did you avoid isolating yourself today?',
            5 => 'Did you spend quality time with someone you trust?'
        ],
        'mind_focus' => [
            1 => 'Did you stay focused on your tasks today?',
            2 => 'Did you avoid distractions while studying or working?',
            3 => 'Did you remember important things without much struggle?',
            4 => 'Did you plan or organize your tasks for the day?',
            5 => 'Did you feel productive and clear-headed today?'
        ],
        'self_care' => [
            1 => 'Did you take time for yourself today (e.g., relaxation, hobbies)?',
            2 => 'Did you practice healthy coping strategies when stressed?',
            3 => 'Did you avoid harmful habits (e.g., overuse of social media, substances)?',
            4 => 'Did you take care of your hygiene and grooming today?',
            5 => 'Did you allow yourself to rest when needed?'
        ],
        'red_flags' => [
            1 => 'Did you feel extremely sad, hopeless, or anxious today?',
            2 => 'Did you lose interest in things you usually enjoy?',
            3 => 'Did you experience changes in appetite or sleep patterns?',
            4 => 'Did you feel isolated or disconnected from others?',
            5 => 'Did you have thoughts of hurting yourself or giving up?'
        ]
    ];
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
        
        $assessmentModel = new MentalHealthAssessment();
        $latestAssessment = $assessmentModel->getLatest($userId);

        // Check if assessment is needed and separate today's vs previous assessments
        $assessmentNeeded = true;
        $todayAssessment = null;
        
        if ($latestAssessment) {
            $lastAssessmentDate = new DateTime($latestAssessment['created_at']);
            $today = new DateTime();
            
            if ($lastAssessmentDate->format('Y-m-d') === $today->format('Y-m-d')) {
                // Assessment was done today - no need for new one
                $assessmentNeeded = false;
                $todayAssessment = $latestAssessment;
            }
        }

        $data = [
            'userEmail' => $userEmail,
            'username' => $username,
            'dayCount' => $dayCount,
            'profilePicUrl' => $profilePicUrl,
            'morningChecklist' => $checklists['morning'],
            'nightChecklist' => $checklists['night'],
            'assessmentSections' => $this->assessmentSections,
            'latestAssessment' => $latestAssessment, // For showing "Last assessment" date
            'todayAssessment' => $todayAssessment,   // For showing today's results (if exists)
            'assessmentNeeded' => $assessmentNeeded
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

    public function saveAssessment()
    {
        try {
            session_start();
            header('Content-Type: application/json');
            
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Unauthorized access', 401);
            }

            // Get and validate input
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data', 400);
            }

            if (empty($input['answers']) || !is_array($input['answers'])) {
                throw new Exception('Missing or invalid answers data', 400);
            }

            $userId = $_SESSION['user_id'];
            $answers = $input['answers'];

            // Validate each section and its answers
            foreach ($this->assessmentSections as $section => $questions) {
                if (!isset($answers[$section])) {
                    throw new Exception("Missing answers for section: $section", 400);
                }

                if (!is_array($answers[$section])) {
                    throw new Exception("Invalid data type for section: $section", 400);
                }

                foreach ($questions as $num => $text) {
                    if (!isset($answers[$section][$num])) {
                        throw new Exception("Missing answer for question $num in section: $section", 400);
                    }

                    // Ensure boolean values
                    $answers[$section][$num] = (bool)$answers[$section][$num];
                }
            }

            // Calculate scores for each section
            $scores = [];
            foreach ($answers as $section => $sectionAnswers) {
                $scores[$section] = MentalHealthAssessment::calculateSectionScore($sectionAnswers);
            }

            // For red flags, invert the score since negative answers are better
            $scores['red_flags'] = 100 - $scores['red_flags'];

            // Calculate total score
            $scores['total'] = array_sum($scores) / count($scores);

            // Generate feedback
            $feedback = MentalHealthAssessment::generateFeedback($scores);

            // Save to database
            $assessmentModel = new MentalHealthAssessment();
            $assessmentId = $assessmentModel->save($userId, $answers, $scores, $feedback);

            if (!$assessmentId) {
                throw new Exception('Failed to save assessment', 500);
            }

            echo json_encode([
                'success' => true,
                'assessment' => [
                    'id' => $assessmentId,
                    'scores' => $scores,
                    'feedback' => $feedback
                ]
            ]);

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage(),
                'error' => $e->getCode()
            ]);
        }
    }

    public function getLatestAssessment()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $userId = $_SESSION['user_id'];
        $assessmentModel = new MentalHealthAssessment();
        $assessment = $assessmentModel->getLatest($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'assessment' => $assessment
        ]);
    }
}