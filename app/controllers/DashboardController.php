<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/MoodLog.php';
require_once __DIR__ . '/../models/BmiLog.php';
require_once __DIR__ . '/../models/Quote.php';
require_once __DIR__ . '/../models/SleepLog.php';
require_once __DIR__ . '/../models/Image.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../../config/constants.php';

class DashboardController extends Controller
{
    public function index()
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
        $moodLogModel = new MoodLog();
        $quoteModel = new Quote();
        $sleepLogModel = new SleepLog();
        $imageModel = new Image();
        $noteModel = new Note();

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

        $latestMoods = $moodLogModel->getLatestByUser($userId, 7);
        $dailyQuote = $quoteModel->getDailyQuote();
        $dailyImage = $imageModel->getDailyImage();
        $sleepHours = $sleepLogModel->getSleepForTodayByUser($userId);
        
        $todayDate = date('Y-m-d');
        $notesStats = $noteModel->getStatsForPeriod($userId, $todayDate, $todayDate);
        $notesCount = $notesStats['count']; 
        $totalNotesAllTime = $noteModel->getStatsForPeriod($userId, '1900-01-01', '2999-12-31')['count']; 
        
        $dayCount = $moodLogModel->getConsecutiveDayCount($userId);
        
        $user = $userModel->findById($userId);
        $userAge = $userModel->calculateAge($user['date_of_birth'] ?? null);
        
        $bmiLogModel = new BmiLog();
        $latestBmi = $bmiLogModel->getLatestBmi($userId);
        $userBmi = $latestBmi['bmi_value'] ?? null;
        
        $sleepRecommendation = null;
        if ($userAge) {
            $sleepRecommendation = $sleepLogModel->getSleepRecommendation($userAge, $userBmi);
        }
        
        $sleepEvaluation = null;
        if ($sleepHours && $userAge) {
            $sleepEvaluation = $sleepLogModel->evaluateSleepQuality($sleepHours, $userAge, $userBmi);
        }
        
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');
        $sleepWeeklyStats = $sleepLogModel->getSleepStatsForPeriod($userId, $startDate, $endDate);
        $recentSleepLogs = $sleepLogModel->getRecentSleepLogs($userId);

        $moodColors = [
            'Lovely' => '#f4bbd9', 'Joy' => '#fefcbf', 'Trust' => '#a7f3d0',
            'Anticipation' => '#fed7aa', 'Unwell' => '#d8b4fe', 'Sad' => '#9ca3af',
            'Fear' => '#6b7280', 'Disgust' => '#84cc16', 'Angry' => '#ef4444',
            'Nonchalant' => '#67e8f9'
        ];

        $data = [
            'userEmail' => $userEmail,
            'username' => $username,
            'profilePicUrl' => $profilePicUrl,
            'dailyQuote' => $dailyQuote,
            'dailyImage' => $dailyImage,
            'moodColors' => $moodColors,
            'latestMoods' => $latestMoods,
            'sleepHours' => $sleepHours,
            'notesCount' => $notesCount,
            'totalNotesAllTime' => $totalNotesAllTime,
            'notesStats' => $notesStats,
            'dayCount' => $dayCount,
            'sleepRecommendation' => $sleepRecommendation,
            'sleepEvaluation' => $sleepEvaluation,
            'sleepWeeklyStats' => $sleepWeeklyStats,
            'recentSleepLogs' => $recentSleepLogs,
            'userAge' => $userAge,
            'userBmi' => $userBmi,
            'sleepLogModel' => $sleepLogModel
        ];

        $this->view('home/dashboard', $data);
    }
}