<?php

class NotificationController
{
    private $model;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        require_once dirname(__DIR__) . '/models/Notification.php';
        $this->model = new Notification();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $notifications = $this->model->getLatestByUser($userId, 3);
        $unreadCount = $this->model->getUnreadCount($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Returns ALL notifications for the current user as JSON.
     * Used by the full notification page for live state updates.
     */
    public function allNotificationsJson()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $filterType = $_GET['type'] ?? null;
        $notifications = $this->model->getAllByUser($userId, $filterType ?: null);
        $unreadCount   = $this->model->getUnreadCount($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount
        ]);
    }

    /**
     * Specialized endpoint for dynamic stats/counters on the notification page.
     */
    public function getCountsJson()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $notifications = $this->model->getAllByUser($userId);
        
        $typeCounts = [
            'all' => count($notifications),
            'booking' => 0,
            'booking_approve' => 0,
            'booking_cancel' => 0,
            'event_update' => 0,
            'message' => 0,
            'event_creation' => 0,
            'feedback' => 0,
            'feedback_reply' => 0
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

        header('Content-Type: application/json');
        echo json_encode($typeCounts);
    }

    public function markAsRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $id = $_GET['id'] ?? null;

        $this->model->markAsRead($userId, $id);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function markAsUnread()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // Handle both single ID (GET) and multiple IDs (POST/JSON)
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['ids'] ?? null;
        }

        $this->model->markAsUnread($userId, $id);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function markAllAsUnread()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $this->model->markAsUnread($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function deleteAll()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $this->model->markAsRead($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function deleteOne()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->delete($_SESSION['user_id'], $id);
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function clearAll()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $this->model->deleteAll($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function allNotifications()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role   = $_SESSION['user_role'] ?? 'client';

        // DISABLED: Automatic mark all as read. 
        // User now handles read/unread manually for privacy.
        // $this->model->markAsRead($userId);

        $filterType    = $_GET['type'] ?? null;
        $notifications = $this->model->getAllByUser($userId, $filterType ?: null);
        $unreadCount   = $this->model->getUnreadCount($userId);

        if ($role === 'admin') {
            require_once dirname(__DIR__) . '/views/admin/all_notifications.php';
        } elseif ($role === 'organizer') {
            require_once dirname(__DIR__) . '/views/organizer/all_notifications.php';
        } else {
            require_once dirname(__DIR__) . '/views/client/all_notifications.php';
        }
    }
}
