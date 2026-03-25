<?php
require_once dirname(dirname(__FILE__)) . '/models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Please enter both email and password.';
            } else {
                $user = $this->userModel->login($email, $password);

                if ($user) {
                    // Login success
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_fullname'] = $user['fullname'];
                    
                    // Redirect to dashboard (or home)
                    header('Location: ' . URL_ROOT . '/dashboard');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        }

        // Load view
        require_once dirname(dirname(__FILE__)) . '/views/auth/login.php';
    }
}
