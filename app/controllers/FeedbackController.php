<?php

class FeedbackController
{
    private $feedbackModel;
    private $notificationModel;
    private $userModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        require_once dirname(__DIR__) . '/models/Feedback.php';
        require_once dirname(__DIR__) . '/models/Notification.php';
        require_once dirname(__DIR__) . '/models/User.php';

        $this->feedbackModel = new Feedback();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }

    private function checkAuth($allowedRoles = [])
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
            header('Location: /EventManagementSystem/public/home');
            exit;
        }
    }

    public function clientFeedback()
    {
        $this->checkAuth(['client']);
        $clientId = $_SESSION['user_id'];
        $feedbacks = $this->feedbackModel->getByClient($clientId);

        require_once dirname(__DIR__) . '/views/client/feedback.php';
    }

    public function store()
    {
        $this->checkAuth(['client']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'client_id' => $_SESSION['user_id'],
                'rating' => $_POST['rating'],
                'comment' => $_POST['comment']
            ];

            $feedbackId = $this->feedbackModel->create($data);
            if ($feedbackId) {
                $clientName = $_SESSION['user_fullname'] ?? 'A client';
                $title = "New Feedback Received";
                $message = "{$clientName} has provided a {$data['rating']}-star rating and feedback.";

                // Notify all admins
                $admins = $this->userModel->getAdmins();
                foreach ($admins as $admin) {
                    $this->notificationModel->create($admin['id'], $title, $message, 'feedback', $feedbackId);
                }

                // Notify all organizers
                $organizers = $this->userModel->getOrganizers();
                foreach ($organizers as $organizer) {
                    $this->notificationModel->create($organizer['id'], $title, $message, 'feedback', $feedbackId);
                }

                $_SESSION['success'] = "Thank you for your feedback!";
            } else {
                $_SESSION['error'] = "Something went wrong. Please try again.";
            }
        }
        header('Location: /EventManagementSystem/public/client/feedback');
        exit;
    }

    public function adminFeedback()
    {
        $this->checkAuth(['admin']);
        $ratingFilter = $_GET['rating'] ?? null;
        $feedbacks = $this->feedbackModel->getAll($ratingFilter);
        $allFeedbacks = $this->feedbackModel->getAll(); 
        $stats = $this->calculateStats($allFeedbacks);
        $activePage = 'feedback';
        require_once dirname(__DIR__) . '/views/admin/feedback.php';
    }

    public function organizerFeedback()
    {
        $this->checkAuth(['organizer']);
        $ratingFilter = $_GET['rating'] ?? null;
        $feedbacks = $this->feedbackModel->getAll($ratingFilter);
        $allFeedbacks = $this->feedbackModel->getAll();
        $stats = $this->calculateStats($allFeedbacks);
        $activePage = 'feedback';
        require_once dirname(__DIR__) . '/views/organizer/feedback.php';
    }

    private function calculateStats($feedbacks)
    {
        $total = count($feedbacks);
        $sum = 0;
        $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        foreach ($feedbacks as $fb) {
            $r = (int)$fb['rating'];
            $sum += $r;
            if (isset($counts[$r])) {
                $counts[$r]++;
            }
        }

        $avg = $total > 0 ? round($sum / $total, 1) : 0;

        return [
            'total' => $total,
            'avg' => $avg,
            'counts' => $counts
        ];
    }

    public function reply()
    {
        $this->checkAuth(['admin', 'organizer', 'client']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $feedbackId = $_POST['feedback_id'];
            $replyText = $_POST['reply'];
            $userId = $_SESSION['user_id'];
            $userRole = $_SESSION['user_role'];
            $parentReplyId = $_POST['parent_reply_id'] ?? null;

            if ($this->feedbackModel->addReply($feedbackId, $userId, $replyText, $parentReplyId)) {
                $feedback = $this->feedbackModel->getById($feedbackId);

                // Determine who to notify
                if ($userRole === 'client') {
                    // Client replied -> Notify Admins and Organizers
                    $title = "Client Replied to Feedback";
                    $message = "{$_SESSION['user_fullname']} has replied to a feedback thread.";

                    $admins = $this->userModel->getAdmins();
                    foreach ($admins as $admin) {
                        $this->notificationModel->create($admin['id'], $title, $message, 'feedback_reply', $feedbackId);
                    }

                    $organizers = $this->userModel->getOrganizers();
                    foreach ($organizers as $organizer) {
                        $this->notificationModel->create($organizer['id'], $title, $message, 'feedback_reply', $feedbackId);
                    }
                } else {
                    $replierRole = ucfirst($userRole);
                    $title = "Response to Your Feedback";
                    $message = "The {$replierRole} has replied to your feedback thread.";
                    $this->notificationModel->create($feedback['client_id'], $title, $message, 'feedback_reply', $feedbackId);
                }
                
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }

    public function editFeedback()
    {
        $this->checkAuth(['client']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['feedback_id'];
            $comment = $_POST['comment'];
            $clientId = $_SESSION['user_id'];

            if ($this->feedbackModel->updateFeedback($id, $comment, $clientId)) {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }

    public function editReply()
    {
        $this->checkAuth(['admin', 'organizer', 'client']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['reply_id'];
            $replyText = $_POST['reply_text'];
            $userId = $_SESSION['user_id'];

            if ($this->feedbackModel->updateReply($id, $replyText, $userId)) {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }
}
