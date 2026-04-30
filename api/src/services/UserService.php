<?php

class UserService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function getAll(): array
    {
        $users = $this->userModel->getAll();
        
        $stats = [
            'total' => $this->userModel->countAll(),
            'clients' => $this->userModel->countByRole('client'),
            'organizers' => $this->userModel->countByRole('organizer'),
            'blocked' => $this->userModel->countBlocked()
        ];

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'users' => $users,
                'stats' => $stats
            ]
        ];
    }

    public function updateRole(int $userId, string $role): array
    {
        if (!in_array($role, ['client', 'organizer', 'admin'])) {
            return ['ok' => false, 'status' => 400, 'message' => 'Invalid role.'];
        }

        if ($this->userModel->updateRole($userId, $role)) {
            return ['ok' => true, 'status' => 200, 'message' => 'User role updated successfully.'];
        }

        return ['ok' => false, 'status' => 500, 'message' => 'Failed to update user role.'];
    }

    public function toggleBlock(int $userId, int $status): array
    {
        if ($this->userModel->toggleBlock($userId, $status)) {
            $msg = $status ? 'User blocked successfully.' : 'User unblocked successfully.';
            return ['ok' => true, 'status' => 200, 'message' => $msg];
        }

        return ['ok' => false, 'status' => 500, 'message' => 'Failed to update user status.'];
    }
}
