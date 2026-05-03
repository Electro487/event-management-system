<?php

class AuthApiController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register(): void
    {
        $result = $this->authService->register(
            trim((string)Request::input('first_name', '')),
            trim((string)Request::input('last_name', '')),
            trim((string)Request::input('email', '')),
            trim((string)Request::input('password', ''))
        );

        $this->respond($result);
    }

    public function login(): void
    {
        $result = $this->authService->login(
            trim((string)Request::input('email', '')),
            trim((string)Request::input('password', ''))
        );

        $this->respond($result);
    }

    public function forgotPassword(): void
    {
        $result = $this->authService->forgotPassword(
            trim((string)Request::input('email', ''))
        );

        $this->respond($result);
    }

    public function verifyOtp(): void
    {
        $result = $this->authService->verifyOtp(
            trim((string)Request::input('email', '')),
            trim((string)Request::input('otp_code', Request::input('otp', ''))),
            trim((string)Request::input('otp_type', 'registration'))
        );

        $this->respond($result);
    }

    public function resetPassword(): void
    {
        $result = $this->authService->resetPassword(
            trim((string)Request::input('email', '')),
            (string)Request::input('password', ''),
            (string)Request::input('confirm_password', '')
        );

        $this->respond($result);
    }

    public function logout(): void
    {
        ApiResponse::success([
            'message' => 'Logged out successfully. Discard token on client.',
        ]);
    }

    public function me(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? null;
        if (!$authUser) {
            ApiResponse::error('Unauthorized', 401, [], 'UNAUTHORIZED');
            return;
        }

        $result = $this->authService->me($authUser);
        $this->respond($result);
    }

    public function updateProfile(): void
    {
        $authUser = $GLOBALS['api_auth_user'];
        $result = $this->authService->updateProfile($authUser, trim((string)Request::input('fullname', '')));
        $this->respond($result);
    }

    public function updateProfilePicture(): void
    {
        $authUser = $GLOBALS['api_auth_user'];
        if (!isset($_FILES['profile_picture'])) {
            ApiResponse::error('No image provided', 422);
            return;
        }
        if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            ApiResponse::error('Upload error: ' . $_FILES['profile_picture']['error'], 400);
            return;
        }
        $result = $this->authService->updateProfilePicture($authUser, $_FILES['profile_picture']);
        $this->respond($result);
    }

    public function deleteProfilePicture(): void
    {
        $authUser = $GLOBALS['api_auth_user'];
        $result = $this->authService->deleteProfilePicture($authUser);
        $this->respond($result);
    }

    private function respond(array $result): void
    {
        if (!($result['ok'] ?? false)) {
            ApiResponse::error(
                $result['message'] ?? 'Request failed.',
                (int)($result['status'] ?? 400),
                $result['errors'] ?? ($result['meta'] ?? []),
                'AUTH_REQUEST_FAILED'
            );
            return;
        }

        ApiResponse::success(
            $result['data'] ?? [],
            (int)($result['status'] ?? 200),
            $result['meta'] ?? []
        );
    }
}
