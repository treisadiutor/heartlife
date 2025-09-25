<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    /**
     * Handles the user registration form submission.
     */
    public function handleSignup()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'signup');
            exit();
        }

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $dateOfBirth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;

        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'All required fields must be filled.';
            header('Location: ' . BASE_URL . 'signup');
            exit();
        }
        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match.';
            header('Location: ' . BASE_URL . 'signup');
            exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format.';
            header('Location: ' . BASE_URL . 'signup');
            exit();
        }
        
        if ($dateOfBirth !== null) {
            $dob = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
            if (!$dob || $dob->format('Y-m-d') !== $dateOfBirth) {
                $_SESSION['error'] = 'Invalid date of birth format.';
                header('Location: ' . BASE_URL . 'signup');
                exit();
            }
            
            $minAge = new DateTime();
            $minAge->sub(new DateInterval('P13Y'));
            if ($dob > $minAge) {
                $_SESSION['error'] = 'You must be at least 13 years old to register.';
                header('Location: ' . BASE_URL . 'signup');
                exit();
            }
            
            $today = new DateTime();
            if ($dob > $today) {
                $_SESSION['error'] = 'Date of birth cannot be in the future.';
                header('Location: ' . BASE_URL . 'signup');
                exit();
            }
        }

        $userModel = new User();
        
        if ($userModel->findByEmail($email)) {
            $_SESSION['error'] = 'An account with this email already exists.';
            header('Location: ' . BASE_URL . 'signup');
            exit();
        }

        if ($userModel->create($username, $email, $password, $dateOfBirth)) {
            $_SESSION['success'] = 'Account created successfully! Please log in.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        } else {
            $_SESSION['error'] = 'Could not create account. The username might be taken.';
            header('Location: ' . BASE_URL . '/signup');
            exit();
        }
    }

    /**
     * Handles the user login form submission.
     */
    public function handleLogin()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['profile_pic'] = $user['profile_pic'] ?? null;
            
            header('Location: ' . BASE_URL . '/dashboard');
            exit();

        } else {
            $_SESSION['error'] = 'Invalid email or password.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }
    
    /**
     * Logs the user out.
     */
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        session_start();
        $_SESSION['success'] = 'You have been logged out successfully.';
        header('Location: ' . BASE_URL . '/login');
        exit();
    }
}