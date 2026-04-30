<?php

class UserApiController
{
    private UserService $service;

    public function __construct()
    {
        $this->service = new UserService();
    }

    public function index(): void
    {
        $this->authorizeAdmin();
        ApiResponse::success($this->service->getAll()['data']);
    }

    public function updateRole(): void
    {
        $this->authorizeAdmin();
        $data = Request::body();

        $userId = (int) ($data['user_id'] ?? 0);
        $role = $data['role'] ?? '';

        if (!$userId || !$role) {
            ApiResponse::error('User ID and role are required.', 400);
            return;
        }

        $result = $this->service->updateRole($userId, $role);
        if ($result['ok']) {
            ApiResponse::success(['message' => $result['message']]);
        } else {
            ApiResponse::error($result['message'], $result['status']);
        }
    }

    public function toggleBlock(): void
    {
        $this->authorizeAdmin();
        $data = Request::body();

        $userId = (int) ($data['user_id'] ?? 0);
        $status = isset($data['status']) ? (int) $data['status'] : -1;

        if (!$userId || $status === -1) {
            ApiResponse::error('User ID and status are required.', 400);
            return;
        }

        $result = $this->service->toggleBlock($userId, $status);
        if ($result['ok']) {
            ApiResponse::success(['message' => $result['message']]);
        } else {
            ApiResponse::error($result['message'], $result['status']);
        }
    }

    private function authorizeAdmin(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        if (($authUser['role'] ?? null) !== 'admin') {
            ApiResponse::error('Forbidden. Admin access required.', 403, [], 'FORBIDDEN');
            exit;
        }
    }
}
