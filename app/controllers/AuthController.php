<?php

class AuthController
{

    private $userModel;

    public function __construct()
    {
        // Need to require User.php inside index.php or here.
        require_once dirname(__DIR__) . '/models/User.php';
        $this->userModel = new User();

        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Init data
            $data = [
                'fullname' => trim($_POST['first_name'] . ' ' . $_POST['last_name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'fullname_err' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                // Check email exists
                if ($this->userModel->emailExists($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }

            // Validate Name
            if (empty($_POST['first_name']) || empty($_POST['last_name'])) {
                $data['fullname_err'] = 'Please enter both first and last name';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Check if errors are empty
            if (empty($data['email_err']) && empty($data['fullname_err']) && empty($data['password_err'])) {
                // Register User
                if ($this->userModel->register($data)) {
                    // Redirect to login
                    header('Location: /EventManagementSystem/public/login');
                    exit;
                } else {
                    die('Something went wrong during registration.');
                }
            } else {
                // For now, we'll just redirect back or show errors. In a real application, you'd pass errors to the view.
                die('Registration errors: ' . implode(', ', array_filter([$data['fullname_err'], $data['email_err'], $data['password_err']])));
            }
        }

        // Load register view for GET requests
        require_once dirname(__DIR__) . '/views/auth/register.php';
    }

    public function login()
    {
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

                    // Redirect to home page (handled by HomeController via index.php)
                    header('Location: ' . URL_ROOT);
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        }

        // Load view
        require_once dirname(__DIR__) . '/views/auth/login.php';
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_fullname']);
        unset($_SESSION['user_role']);
        session_destroy();
        header('Location: /EventManagementSystem/public/login');
        exit;
    }
}
