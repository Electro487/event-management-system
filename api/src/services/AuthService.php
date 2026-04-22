<?php

class AuthService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(string $firstName, string $lastName, string $email, string $password): array
    {
        $data = [
            'fullname' => trim($firstName . ' ' . $lastName),
            'email' => trim($email),
            'password' => $password,
        ];

        $errors = [];
        if (empty($firstName) || empty($lastName)) {
            $errors['fullname'] = 'Please enter both first and last name';
        }
        if (empty($data['email'])) {
            $errors['email'] = 'Please enter email';
        } elseif ($this->userModel->emailExists($data['email'])) {
            $errors['email'] = 'Email is already taken';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'Please enter password';
        } elseif (strlen($data['password']) < 7) {
            $errors['password'] = 'Password must be at least 7 characters';
        } elseif (!preg_match('/[A-Z]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one capital letter (A-Z)';
        } elseif (!preg_match('/[0-9]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one number (0-9)';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'status' => 422, 'message' => 'Validation failed.', 'errors' => $errors];
        }

        if (!$this->userModel->register($data)) {
            return ['ok' => false, 'status' => 500, 'message' => 'Something went wrong during registration.'];
        }

        $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        if (!$this->userModel->updateOTP($data['email'], $otp, $expiresAt)) {
            return ['ok' => false, 'status' => 500, 'message' => 'Something went wrong while generating OTP.'];
        }

        if (!MailHelper::sendOTP($data['email'], $otp)) {
            return ['ok' => false, 'status' => 500, 'message' => 'Failed to send verification email. Please try again.'];
        }

        return [
            'ok' => true,
            'status' => 201,
            'data' => [
                'email' => $data['email'],
                'otp_required' => true,
                'otp_expires_in_seconds' => 600,
            ],
        ];
    }

    public function login(string $email, string $password): array
    {
        if ($email === '' || $password === '') {
            return ['ok' => false, 'status' => 422, 'message' => 'Please enter both email and password.'];
        }

        $user = $this->userModel->login($email, $password);
        if (!$user) {
            return ['ok' => false, 'status' => 401, 'message' => 'Invalid email or password.'];
        }

        if (!empty($user['is_blocked'])) {
            return ['ok' => false, 'status' => 403, 'message' => 'Your account has been blocked. Please contact support.'];
        }

        if (!(int)$user['is_verified']) {
            $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $this->userModel->updateOTP($user['email'], $otp, $expiresAt);
            MailHelper::sendOTP($user['email'], $otp);

            return [
                'ok' => false,
                'status' => 403,
                'message' => 'Please verify your email address before logging in. A new OTP has been sent.',
                'meta' => ['requires_otp' => true],
            ];
        }

        $token = JwtHelper::issue([
            'sub' => (int)$user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => (int)$user['id'],
                    'email' => $user['email'],
                    'fullname' => $user['fullname'],
                    'role' => $user['role'],
                    'profile_picture' => $user['profile_picture'] ?? null,
                ],
            ],
        ];
    }

    public function forgotPassword(string $email): array
    {
        if ($email === '') {
            return ['ok' => false, 'status' => 422, 'message' => 'Please enter your email.'];
        }
        if (!$this->userModel->emailExists($email)) {
            return ['ok' => false, 'status' => 404, 'message' => 'No account found with that email.'];
        }

        $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        if (!$this->userModel->updateOTP($email, $otp, $expiresAt)) {
            return ['ok' => false, 'status' => 500, 'message' => 'Something went wrong.'];
        }
        if (!MailHelper::sendOTP($email, $otp)) {
            return ['ok' => false, 'status' => 500, 'message' => 'Failed to send OTP. Please try again later.'];
        }

        return [
            'ok' => true,
            'status' => 200,
            'data' => ['email' => $email, 'otp_type' => 'password_reset', 'otp_expires_in_seconds' => 600],
        ];
    }

    public function verifyOtp(string $email, string $otp, string $otpType = 'registration'): array
    {
        if ($email === '') {
            return ['ok' => false, 'status' => 422, 'message' => 'Email is required.'];
        }
        if (strlen($otp) !== 6) {
            return ['ok' => false, 'status' => 422, 'message' => 'Please enter a valid 6-digit code.'];
        }
        if (!$this->userModel->verifyOTP($email, $otp)) {
            return ['ok' => false, 'status' => 422, 'message' => 'Invalid or expired OTP.'];
        }

        if ($otpType !== 'password_reset') {
            $this->userModel->markEmailAsVerified($email);
        }

        return ['ok' => true, 'status' => 200, 'data' => ['verified' => true, 'otp_type' => $otpType]];
    }

    public function resetPassword(string $email, string $password, string $confirmPassword): array
    {
        if ($email === '') {
            return ['ok' => false, 'status' => 422, 'message' => 'Email is required.'];
        }
        if ($password === '' || strlen($password) < 6) {
            return ['ok' => false, 'status' => 422, 'message' => 'Password must be at least 6 characters.'];
        }
        if ($password !== $confirmPassword) {
            return ['ok' => false, 'status' => 422, 'message' => 'Passwords do not match.'];
        }
        if (!$this->userModel->resetPassword($email, $password)) {
            return ['ok' => false, 'status' => 500, 'message' => 'Failed to reset password.'];
        }

        return ['ok' => true, 'status' => 200, 'data' => ['password_reset' => true]];
    }

    public function me(array $authUser): array
    {
        return [
            'ok' => true,
            'status' => 200,
            'data' => ['user' => $authUser],
        ];
    }
}
