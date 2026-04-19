<?php

class AuthController
{

    private $userModel;

    public function __construct()
    {
        // Need to require User.php inside index.php or here.
        require_once dirname(__DIR__) . '/models/User.php';
        require_once dirname(__DIR__) . '/helpers/MailHelper.php';
        $this->userModel = new User();

        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function redirectIfLoggedIn()
    {
        if (isset($_SESSION['user_id'])) {
            $role = $_SESSION['user_role'] ?? 'client';
            if ($role === 'admin') {
                header('Location: /EventManagementSystem/public/admin/dashboard');
            } elseif ($role === 'organizer') {
                header('Location: /EventManagementSystem/public/organizer/dashboard');
            } else {
                header('Location: /EventManagementSystem/public/client/home');
            }
            exit;
        }
    }

    public function register()
    {
        $this->redirectIfLoggedIn();
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
            } elseif (strlen($data['password']) < 7) {
                $data['password_err'] = 'Password must be at least 7 characters';
            } elseif (!preg_match('/[A-Z]/', $data['password'])) {
                $data['password_err'] = 'Password must contain at least one capital letter (A-Z)';
            } elseif (!preg_match('/[0-9]/', $data['password'])) {
                $data['password_err'] = 'Password must contain at least one number (0-9)';
            }

            // Check if errors are empty
            if (empty($data['email_err']) && empty($data['fullname_err']) && empty($data['password_err'])) {
                // Register User
                if ($this->userModel->register($data)) {
                    // Generate and Send OTP
                    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));
                    
                    if ($this->userModel->updateOTP($data['email'], $otp, $expires_at)) {
                        if (MailHelper::sendOTP($data['email'], $otp)) {
                            $_SESSION['otp_email'] = $data['email'];
                            header('Location: /EventManagementSystem/public/verify-otp');
                            exit;
                        } else {
                            $error = 'Failed to send verification email. Please try again.';
                        }
                    } else {
                        $error = 'Something went wrong while generating OTP.';
                    }
                } else {
                    $error = 'Something went wrong during registration.';
                }
            } else {
                 $error = implode(', ', array_filter([$data['fullname_err'], $data['email_err'], $data['password_err']]));
            }
            
            if (!empty($error)) {
                 $_SESSION['error'] = $error;
                 require_once dirname(__DIR__) . '/views/auth/register.php';
                 return;
            }
        }

        // Load register view for GET requests
        require_once dirname(__DIR__) . '/views/auth/register.php';
    }

    public function login()
    {
        $this->redirectIfLoggedIn();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Please enter both email and password.';
            } else {
                $user = $this->userModel->login($email, $password);

                if ($user) {
                    // Check if blocked
                    if (!empty($user['is_blocked'])) {
                        $error = 'Your account has been blocked. Please contact support.';
                    }
                    // Check if verified
                    elseif (!$user['is_verified']) {
                        // Generate new OTP and redirect to verification
                        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                        $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));
                        $this->userModel->updateOTP($user['email'], $otp, $expires_at);
                        MailHelper::sendOTP($user['email'], $otp);
                        
                        $_SESSION['otp_email'] = $user['email'];
                        $_SESSION['error'] = 'Please verify your email address before logging in. A new OTP has been sent.';
                        header('Location: /EventManagementSystem/public/verify-otp');
                        exit;
                    }

                    if (empty($error)) {
                        // Login success
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['user_fullname'] = $user['fullname'];
                        $_SESSION['user_profile_pic'] = $user['profile_picture'] ?? null;

                        // Redirect based on role
                        $role = $user['role'];
                        if ($role === 'admin') {
                            $redirect = '/EventManagementSystem/public/admin/dashboard';
                        } elseif ($role === 'organizer') {
                            $redirect = '/EventManagementSystem/public/organizer/dashboard';
                        } else {
                            $redirect = '/EventManagementSystem/public/client/home';
                        }
                        header('Location: ' . $redirect);
                        exit;
                    }
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        }

        // Load view
        require_once dirname(__DIR__) . '/views/auth/login.php';
    }

    public function forgotPassword()
    {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $error = 'Please enter your email.';
            } elseif (!$this->userModel->emailExists($email)) {
                $error = 'No account found with that email.';
            } else {
                $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                if ($this->userModel->updateOTP($email, $otp, $expires_at)) {
                    if (MailHelper::sendOTP($email, $otp)) {
                        $_SESSION['otp_email'] = $email;
                        $_SESSION['otp_type'] = 'password_reset';
                        header('Location: /EventManagementSystem/public/verify-otp');
                        exit;
                    } else {
                        $error = 'Failed to send OTP. Please try again later.';
                    }
                } else {
                    $error = 'Something went wrong.';
                }
            }
        }

        require_once dirname(__DIR__) . '/views/auth/forgot_password.php';
    }

    public function verifyOtp()
    {
        $error = '';
        $email = $_SESSION['otp_email'] ?? '';

        if (empty($email)) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect OTP from multiple inputs if using individual boxes
            if (isset($_POST['otp_code'])) {
                $otp = trim($_POST['otp_code']);
            } else {
                $otp = trim(implode('', $_POST['otp'] ?? []));
            }

            if (strlen($otp) !== 6) {
                $error = 'Please enter a valid 6-digit code.';
            } elseif ($this->userModel->verifyOTP($email, $otp)) {
                if (isset($_SESSION['otp_type']) && $_SESSION['otp_type'] === 'password_reset') {
                    // Redirect to reset password
                    header('Location: /EventManagementSystem/public/reset-password');
                    exit;
                } else {
                    // Mark as verified and login
                    $this->userModel->markEmailAsVerified($email);
                    unset($_SESSION['otp_email']);
                    $_SESSION['success'] = 'Email verified successfully! You can now login.';
                    header('Location: /EventManagementSystem/public/login');
                    exit;
                }
            } else {
                $error = 'Invalid or expired OTP.';
            }
        }

        require_once dirname(__DIR__) . '/views/auth/verify_otp.php';
    }

    public function resetPassword()
    {
        $error = '';
        $email = $_SESSION['otp_email'] ?? '';

        if (empty($email) || !isset($_SESSION['otp_type']) || $_SESSION['otp_type'] !== 'password_reset') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($password) || strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } elseif ($password !== $confirm_password) {
                $error = 'Passwords do not match.';
            } else {
                if ($this->userModel->resetPassword($email, $password)) {
                    unset($_SESSION['otp_email']);
                    unset($_SESSION['otp_type']);
                    $_SESSION['success'] = 'Password reset successfully! You can now login.';
                    header('Location: /EventManagementSystem/public/login');
                    exit;
                } else {
                    $error = 'Failed to reset password.';
                }
            }
        }

        require_once dirname(__DIR__) . '/views/auth/reset_password.php';
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
