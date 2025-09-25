<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/MoodLog.php';
require_once __DIR__ . '/../models/SleepLog.php';
require_once __DIR__ . '/../models/BmiLog.php';
require_once __DIR__ . '/../models/Checklist.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/User.php';

class ReportController extends Controller
{
    public function report()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        $userId = $_SESSION['user_id'];

        $dateRange = $_GET['range'] ?? 'last_30_days';
        $endDate = date('Y-m-d');
        
        switch ($dateRange) {
            case 'last_7_days':
                $startDate = date('Y-m-d', strtotime('-6 days'));
                break;
            case 'this_month':
                $startDate = date('Y-m-01');
                break;
            case 'custom':
                $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-29 days'));
                $endDate = $_GET['end_date'] ?? date('Y-m-d');
                break;
            case 'last_30_days':
            default:
                $startDate = date('Y-m-d', strtotime('-29 days'));
                break;
        }

        $moodLogModel = new MoodLog();
        $sleepLogModel = new SleepLog();
        $bmiLogModel = new BmiLog();
        $checklistModel = new Checklist();
        $noteModel = new Note();
        $userModel = new User();

        $username = $_SESSION['username'];
        $dayCount = $moodLogModel->getConsecutiveDayCount($userId);
        
        $userData = $userModel->findById($userId);
        
        $data = [
            'username' => $username,
            'dayCount' => $dayCount,
            'moodStats' => $moodLogModel->getStatsForPeriod($userId, $startDate, $endDate),
            'sleepStats' => $sleepLogModel->getSleepStatsForPeriod($userId, $startDate, $endDate),
            'checklistStats' => $checklistModel->getStatsForPeriod($userId, $startDate, $endDate),
            'bmiStats' => $bmiLogModel->getStatsForPeriod($userId, $startDate, $endDate),
            'notesStats' => $noteModel->getStatsForPeriod($userId, $startDate, $endDate),
            'userData' => $userData,
            'dateRange' => [
                'start' => $startDate,
                'end' => $endDate,
                'selected' => $dateRange
            ]
        ];
        
        $this->view('reports/index', $data);
    }
}