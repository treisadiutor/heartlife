<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/BmiLog.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../models/MoodLog.php';
    
class ProfileController extends Controller {
    private $userModel;
    private $bmiLogModel;

    public function __construct() {
        $this->userModel = new User();
        $this->bmiLogModel = new BmiLog();
    }

    /**
     * Display the profile management page
     */
    public function index() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $user = $this->userModel->findById($userId);
        $bmiLogs = $this->bmiLogModel->getAllByUserId($userId);
        
        $moodLogModel = new MoodLog();
        $dayCount = $moodLogModel->getConsecutiveDayCount($userId);

        if (!$user) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $data = [
            'user' => $user,
            'username' => $user['username'],
            'dayCount' => $dayCount,
            'bmiLogs' => $bmiLogs,
            'pageTitle' => 'Profile Management'
        ];

        $this->view('profile/index', $data);
    }

    /**
     * Update user profile information
     */
    public function updateProfile() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $dateOfBirth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;

        $errors = [];
        if (empty($username)) {
            $errors[] = 'Username is required';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        
        if ($dateOfBirth !== null) {
            $dob = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
            if (!$dob || $dob->format('Y-m-d') !== $dateOfBirth) {
                $errors[] = 'Invalid date of birth format';
            } else {
                $minAge = new DateTime();
                $minAge->sub(new DateInterval('P13Y'));
                if ($dob > $minAge) {
                    $errors[] = 'You must be at least 13 years old';
                }
                
                $today = new DateTime();
                if ($dob > $today) {
                    $errors[] = 'Date of birth cannot be in the future';
                }
            }
        }

        if (empty($errors)) {
            if ($this->userModel->updateProfile($userId, $username, $email, $dateOfBirth)) {
                $_SESSION['success'] = 'Profile updated successfully';
                
                if ($username !== $_SESSION['username']) {
                    $_SESSION['username'] = $username;
                }
            } else {
                $_SESSION['error'] = 'Failed to update profile. Username or email might already be in use.';
            }
        } else {
            $_SESSION['error'] = implode(', ', $errors);
        }

        header('Location: ' . BASE_URL . '/profile');
        exit();
    }

    /**
     * Update user password
     */
    public function updatePassword() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required';
        } elseif (!$this->userModel->verifyPassword($userId, $currentPassword)) {
            $errors[] = 'Current password is incorrect';
        }
        
        if (empty($newPassword)) {
            $errors[] = 'New password is required';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters long';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Password confirmation does not match';
        }

        if (empty($errors)) {
            if ($this->userModel->updatePassword($userId, $newPassword)) {
                $_SESSION['success'] = 'Password updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update password';
            }
        } else {
            $_SESSION['error'] = implode(', ', $errors);
        }

        header('Location: ' . BASE_URL . '/profile');
        exit();
    }

    /**
     * Upload and update profile picture
     */
    public function updateProfilePicture() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $userId = $_SESSION['user_id'];
        
        if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Please select a valid image file';
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $file = $_FILES['profile_pic'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = 'Only JPG, PNG, and GIF files are allowed';
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'File size must be less than 5MB';
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $uploadDir = __DIR__ . '/../../assets/images/profile/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;
        $relativePath = 'assets/images/profile/' . $filename;

        $oldPicture = $this->userModel->getProfilePicture($userId);
        if ($oldPicture && file_exists(__DIR__ . '/../../' . $oldPicture)) {
            unlink(__DIR__ . '/../../' . $oldPicture);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            if ($this->userModel->updateProfilePicture($userId, $relativePath)) {
                $_SESSION['profile_picture'] = $relativePath;
                $_SESSION['success'] = 'Profile picture updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update profile picture in database';
            }
        } else {
            $_SESSION['error'] = 'Failed to upload file';
        }

        header('Location: ' . BASE_URL . '/profile');
        exit();
    }

    /**
     * Add new BMI log entry
     */
    public function addBmiLog() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $height = floatval($_POST['height_cm'] ?? 0);
        $weight = floatval($_POST['weight_kg'] ?? 0);
        $logDate = $_POST['log_date'] ?? date('Y-m-d');

        $errors = [];
        if ($height <= 0 || $height > 300) {
            $errors[] = 'Height must be between 1 and 300 cm';
        }
        if ($weight <= 0 || $weight > 1000) {
            $errors[] = 'Weight must be between 1 and 1000 kg';
        }
        if (!strtotime($logDate)) {
            $errors[] = 'Invalid date format';
        }

        if (empty($errors)) {
            $bmiValue = $weight / (($height / 100) ** 2);
            
            if ($this->bmiLogModel->create($userId, $height, $weight, $bmiValue, $logDate)) {
                $_SESSION['success'] = 'BMI log added successfully';
            } else {
                $_SESSION['error'] = 'Failed to add BMI log';
            }
        } else {
            $_SESSION['error'] = implode(', ', $errors);
        }

        header('Location: ' . BASE_URL . '/profile');
        exit();
    }

    /**
     * Update BMI log entry
     */
    public function updateBmiLog() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $logId = intval($_POST['log_id'] ?? 0);
        $height = floatval($_POST['height_cm'] ?? 0);
        $weight = floatval($_POST['weight_kg'] ?? 0);
        $logDate = $_POST['log_date'] ?? '';

        $existingLog = $this->bmiLogModel->getById($logId, $userId);
        if (!$existingLog) {
            $_SESSION['error'] = 'BMI log not found';
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $errors = [];
        if ($height <= 0 || $height > 300) {
            $errors[] = 'Height must be between 1 and 300 cm';
        }
        if ($weight <= 0 || $weight > 1000) {
            $errors[] = 'Weight must be between 1 and 1000 kg';
        }
        if (!strtotime($logDate)) {
            $errors[] = 'Invalid date format';
        }

        if (empty($errors)) {
            $bmiValue = $weight / (($height / 100) ** 2);
            
            if ($this->bmiLogModel->update($logId, $height, $weight, $bmiValue, $logDate)) {
                $_SESSION['success'] = 'BMI log updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update BMI log';
            }
        } else {
            $_SESSION['error'] = implode(', ', $errors);
        }

        header('Location: ' . BASE_URL . '/profile');
        exit();
    }

    /**
     * Delete BMI log entry
     */
    public function deleteBmiLog() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $logId = intval($_POST['log_id'] ?? 0);

        if ($this->bmiLogModel->delete($logId, $userId)) {
            $_SESSION['success'] = 'BMI log deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete BMI log';
        }

        header('Location: ' . BASE_URL . '/profile');
        exit();
    }
}