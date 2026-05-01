<?php

class NotificationService
{
    private Notification $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    public function latest(array $authUser): array
    {
        $userId = (int)($authUser['id'] ?? 0);
        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'notifications' => $this->notificationModel->getLatestByUser($userId, 15),
                'unreadCount' => (int)$this->notificationModel->getUnreadCount($userId),
            ],
        ];
    }

    public function list(array $authUser, ?string $type): array
    {
        $userId = (int)($authUser['id'] ?? 0);
        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'notifications' => $this->notificationModel->getAllByUser($userId, $type ?: null),
                'unreadCount' => (int)$this->notificationModel->getUnreadCount($userId),
            ],
        ];
    }

    public function counts(array $authUser): array
    {
        $userId = (int)($authUser['id'] ?? 0);
        $notifications = $this->notificationModel->getAllByUser($userId);

        $typeCounts = [
            'all' => count($notifications),
            'booking' => 0,
            'booking_approve' => 0,
            'booking_cancel' => 0,
            'event_update' => 0,
            'message' => 0,
            'event_creation' => 0,
        ];

        foreach ($notifications as $n) {
            $t = $n['type'] ?: 'info';
            if (isset($typeCounts[$t])) {
                $typeCounts[$t]++;
            }
            if ($t === 'event') {
                $typeCounts['event_creation']++;
            }
        }

        return ['ok' => true, 'status' => 200, 'data' => $typeCounts];
    }

    public function markRead(array $authUser, int $id): array
    {
        $this->notificationModel->markAsRead((int)$authUser['id'], $id > 0 ? $id : null);
        return ['ok' => true, 'status' => 200, 'data' => ['success' => true]];
    }

    public function markUnread(array $authUser, int $id): array
    {
        $this->notificationModel->markAsUnread((int)$authUser['id'], $id > 0 ? $id : null);
        return ['ok' => true, 'status' => 200, 'data' => ['success' => true]];
    }

    public function markAllRead(array $authUser): array
    {
        $this->notificationModel->markAsRead((int)$authUser['id']);
        return ['ok' => true, 'status' => 200, 'data' => ['success' => true]];
    }

    public function markAllUnread(array $authUser): array
    {
        $this->notificationModel->markAsUnread((int)$authUser['id']);
        return ['ok' => true, 'status' => 200, 'data' => ['success' => true]];
    }

    public function deleteOne(array $authUser, int $id): array
    {
        if ($id <= 0) {
            return ['ok' => false, 'status' => 422, 'message' => 'Notification id is required.'];
        }
        $this->notificationModel->delete((int)$authUser['id'], $id);
        return ['ok' => true, 'status' => 200, 'data' => ['success' => true]];
    }

    public function deleteAll(array $authUser): array
    {
        $this->notificationModel->deleteAll((int)$authUser['id']);
        return ['ok' => true, 'status' => 200, 'data' => ['success' => true]];
    }
}
