<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/SleepLog.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ .  '/../models/BmiLog.php';
require_once __DIR__ . '/../../config/constants.php';

class SleepController extends Controller
{
    private $sleepLogModel;
    private $userModel;
    private $bmiLogModel;

    public function __construct()
    {
        $this->sleepLogModel = new SleepLog();
        $this->userModel = new User();
        $this->bmiLogModel = new BmiLog();
    }

    /**
     * Display the sleep tracker page
     */
    public function index(): void
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
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
        
        
        $userData = $this->userModel->findById($userId);
        $age = $this->userModel->calculateAge($userData['date_of_birth'] ?? null);
        
        $latestBmi = $this->bmiLogModel->getLatestBmi($userId);
        $bmi = $latestBmi['bmi_value'] ?? null;
        
        $recommendation = null;
        if ($age) {
            $recommendation = $this->sleepLogModel->getSleepRecommendation($age, $bmi);
        }
        
        $today = date('Y-m-d');
        $todaysLog = $this->sleepLogModel->getSleepLogByDate($userId, $today);
        
        $recentLogs = $this->sleepLogModel->getRecentSleepLogs($userId);
        
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');
        $weeklyStats = $this->sleepLogModel->getSleepStatsForPeriod($userId, $startDate, $endDate);
        
        $todaysEvaluation = null;
        if ($todaysLog && $age) {
            $todaysEvaluation = $this->sleepLogModel->evaluateSleepQuality(
                (float)$todaysLog['hours'], 
                $age, 
                $bmi
            );
        }
        
        $this->view('sleepTracker/index', [
            'userData' => $userData,
            'userEmail' => $userEmail,
            'username' => $username,
            'profilePicUrl' => $profilePicUrl,
            'recommendation' => $recommendation,
            'todaysLog' => $todaysLog,
            'todaysEvaluation' => $todaysEvaluation,
            'recentLogs' => $recentLogs,
            'weeklyStats' => $weeklyStats,
            'age' => $age,
            'bmi' => $bmi
        ]);
    }

    /**
     * Handle sleep log creation/update
     */
    public function logSleep(): void
    {
        session_start();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $userId = $_SESSION['user_id'];
        $hours = $_POST['hours'] ?? null;
        $logDate = $_POST['log_date'] ?? date('Y-m-d');

        if (!$hours || !is_numeric($hours) || $hours <= 0 || $hours > 24) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid hours. Please enter a number between 0.1 and 24.']);
            return;
        }

        if (!$this->isValidDate($logDate)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            return;
        }

        if (strtotime($logDate) > strtotime(date('Y-m-d'))) {
            http_response_code(400);
            echo json_encode(['error' => 'Cannot log sleep for future dates']);
            return;
        }

        $success = $this->sleepLogModel->createSleepLog($userId, (float)$hours, $logDate);

        if ($success) {
            $userData = $this->userModel->findById($userId);
            $age = $this->userModel->calculateAge($userData['date_of_birth'] ?? null);
            
            $latestBmi = $this->bmiLogModel->getLatestBmi($userId);
            $bmi = $latestBmi['bmi_value'] ?? null;
            
            $evaluation = null;
            if ($age) {
                $evaluation = $this->sleepLogModel->evaluateSleepQuality((float)$hours, $age, $bmi);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Sleep logged successfully!',
                'evaluation' => $evaluation
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to log sleep']);
        }
    }

    /**
     * Get sleep statistics for a period
     */
    public function getStats(): void
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $userId = $_SESSION['user_id'];
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        if (!$this->isValidDate($startDate) || !$this->isValidDate($endDate)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            return;
        }

        $stats = $this->sleepLogModel->getSleepStatsForPeriod($userId, $startDate, $endDate);
        $logs = $this->sleepLogModel->getSleepLogsForPeriod($userId, $startDate, $endDate);
        
        $userData = $this->userModel->findById($userId);
        $age = $userData['age'] ?? null;
        
        $latestBmi = $this->bmiLogModel->getLatestBmi($userId);
        $bmi = $latestBmi['bmi_value'] ?? null;
        
        $recommendation = null;
        if ($age) {
            $recommendation = $this->sleepLogModel->getSleepRecommendation($age, $bmi);
        }

        echo json_encode([
            'stats' => $stats,
            'logs' => $logs,
            'recommendation' => $recommendation,
            'period' => [
                'startDate' => $startDate,
                'endDate' => $endDate
            ]
        ]);
    }

    /**
     * Delete a sleep log entry
     */
    public function deleteSleep(): void
    {
        session_start();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $userId = $_SESSION['user_id'];
        $logDate = $_POST['log_date'] ?? null;

        if (!$logDate || !$this->isValidDate($logDate)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date']);
            return;
        }

        $success = $this->sleepLogModel->deleteSleepLog($userId, $logDate);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Sleep log deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete sleep log']);
        }
    }

    /**
     * Get sleep recommendation for current user
     */
    public function getRecommendation(): void
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $userId = $_SESSION['user_id'];
        
        $userData = $this->userModel->findById($userId);
        $age = $this->userModel->calculateAge($userData['date_of_birth'] ?? null);
        
        if (!$age) {
            http_response_code(400);
            echo json_encode(['error' => 'Date of birth not set. Please update your profile.']);
            return;
        }
        
        $latestBmi = $this->bmiLogModel->getLatestBmi($userId);
        $bmi = $latestBmi['bmi_value'] ?? null;
        
        $recommendation = $this->sleepLogModel->getSleepRecommendation($age, $bmi);
        
        echo json_encode([
            'recommendation' => $recommendation,
            'userInfo' => [
                'age' => $age,
                'bmi' => $bmi,
                'bmiDate' => $latestBmi['log_date'] ?? null
            ]
        ]);
    }

    /**
     * Validate date format
     */
    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}