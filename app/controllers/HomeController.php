<?php
require_once __DIR__ . '/../../core/Controller.php';

class HomeController extends Controller
{
    public function index()
    {
        
        session_start();
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }

        $this->view('public/index');
    }

    public function login(): void
    {
        session_start();
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }

        $this->view('public/login');
    }

    public function signup(): void
    {
        session_start();
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        
        $this->view('public/signup');
    }
}