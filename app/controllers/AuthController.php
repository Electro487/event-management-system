<?php

class AuthController {
    
    private $userModel;

    public function __construct() {
        // Need to require User.php inside index.php or here.
        require_once dirname(__DIR__) . '/models/User.php';
        $this->userModel = new User();
    }

    public function register() {
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
    }
}
