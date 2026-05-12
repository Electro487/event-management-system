<?php

class FeedbackService
{
    private Feedback $feedbackModel;
    private Notification $notificationModel;
    private User $userModel;

    public function __construct()
    {
        $this->feedbackModel = new Feedback();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }

    public function getAll(?int $rating = null, int $page = 1, int $limit = 10): array
    {
        $feedbacks = $this->feedbackModel->getAll($rating, $page, $limit);
        $total = $this->feedbackModel->getTotalCount($rating);
        
        return [
            'ok' => true,
            'status' => 200,
            'data' => $feedbacks,
            'pagination' => [
                'total' => $total,
                'totalPages' => ceil($total / $limit),
                'currentPage' => $page,
                'limit' => $limit
            ]
        ];
    }

    public function getByClient(int $clientId, int $page = 1, int $limit = 10): array
    {
        $feedbacks = $this->feedbackModel->getByClient($clientId, $page, $limit);
        $total = $this->feedbackModel->getTotalCountByClient($clientId);
        
        return [
            'ok' => true,
            'status' => 200,
            'data' => $feedbacks,
            'pagination' => [
                'total' => $total,
                'totalPages' => ceil($total / $limit),
                'currentPage' => $page,
                'limit' => $limit
            ]
        ];
    }

    public function create(array $authUser, array $data): array
    {
        if (empty($data['rating']) || empty($data['comment'])) {
            return ['ok' => false, 'status' => 422, 'message' => 'Rating and comment are required.'];
        }

        $feedbackData = [
            'client_id' => $authUser['id'],
            'rating' => $data['rating'],
            'comment' => $data['comment']
        ];

        $feedbackId = $this->feedbackModel->create($feedbackData);
        if ($feedbackId) {
            $clientName = $authUser['fullname'] ?? 'A client';
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

            // Handle Mentions
            $this->processMentions($data['comment'], (int) $feedbackId, $authUser);

            return ['ok' => true, 'status' => 201, 'data' => ['id' => $feedbackId, 'message' => 'Thank you for your feedback!']];
        }

        return ['ok' => false, 'status' => 500, 'message' => 'Failed to create feedback.'];
    }

    public function addReply(array $authUser, array $data): array
    {
        if (empty($data['feedback_id']) || empty($data['reply'])) {
            return ['ok' => false, 'status' => 422, 'message' => 'Feedback ID and reply text are required.'];
        }

        $feedbackId = $data['feedback_id'];
        $replyText = $data['reply'];
        $userId = $authUser['id'];
        $userRole = $authUser['role'];
        $parentReplyId = $data['parent_reply_id'] ?? null;

        if ($this->feedbackModel->addReply($feedbackId, $userId, $replyText, $parentReplyId)) {
            $feedback = $this->feedbackModel->getById($feedbackId);

            // Notify
            if ($userRole === 'client') {
                $title = "Client Replied to Feedback";
                $message = "{$authUser['fullname']} has replied to a feedback thread.";

                foreach ($this->userModel->getAdmins() as $admin) {
                    $this->notificationModel->create($admin['id'], $title, $message, 'feedback_reply', $feedbackId);
                }
                foreach ($this->userModel->getOrganizers() as $organizer) {
                    $this->notificationModel->create($organizer['id'], $title, $message, 'feedback_reply', $feedbackId);
                }
            } else {
                $replierRole = ucfirst($userRole);
                $title = "Response to Your Feedback";
                $message = "The {$replierRole} has replied to your feedback thread.";
                $this->notificationModel->create($feedback['client_id'], $title, $message, 'feedback_reply', $feedbackId);

                // Cross-notification between Admin and Organizer
                $crossTitle = "{$replierRole} Replied to Feedback";
                $crossMessage = "{$authUser['fullname']} ({$replierRole}) has replied to a feedback thread.";

                if ($userRole === 'organizer') {
                    // Notify all admins when an organizer replies
                    foreach ($this->userModel->getAdmins() as $admin) {
                        $this->notificationModel->create($admin['id'], $crossTitle, $crossMessage, 'feedback_reply', $feedbackId);
                    }
                } elseif ($userRole === 'admin') {
                    // Notify all organizers when an admin replies
                    foreach ($this->userModel->getOrganizers() as $organizer) {
                        $this->notificationModel->create($organizer['id'], $crossTitle, $crossMessage, 'feedback_reply', $feedbackId);
                    }
                }
            }

            // Handle Mentions
            $this->processMentions($replyText, (int) $feedbackId, $authUser);

            return ['ok' => true, 'status' => 201, 'data' => ['success' => true]];
        }

        return ['ok' => false, 'status' => 500, 'message' => 'Failed to add reply.'];
    }

    public function updateFeedback(array $authUser, array $data): array
    {
        if (empty($data['feedback_id']) || empty($data['comment'])) {
            return ['ok' => false, 'status' => 422, 'message' => 'Feedback ID and comment are required.'];
        }

        if ($this->feedbackModel->updateFeedback($data['feedback_id'], $data['comment'], $authUser['id'])) {
            return ['ok' => true, 'status' => 200, 'data' => ['success' => true]];
        }

        return ['ok' => false, 'status' => 500, 'message' => 'Failed to update feedback.'];
    }

    public function updateReply(array $authUser, array $data): array
    {
        if (empty($data['reply_id']) || empty($data['reply_text'])) {
            return ['ok' => false, 'status' => 422, 'message' => 'Reply ID and text are required.'];
        }

        if ($this->feedbackModel->updateReply($data['reply_id'], $data['reply_text'], $authUser['id'])) {
            return ['ok' => true, 'status' => 200, 'data' => ['success' => true]];
        }

        return ['ok' => false, 'status' => 500, 'message' => 'Failed to update reply.'];
    }

    public function getStats(): array
    {
        $stats = $this->feedbackModel->getRatingStats();

        return [
            'ok' => true,
            'status' => 200,
            'data' => $stats
        ];
    }

    private function processMentions(string $text, int $feedbackId, array $sender): void
    {
        $senderName = $sender['fullname'] ?? 'A user';
        $senderId = (int) ($sender['id'] ?? 0);

        // Handle @admin
        if (stripos($text, '@admin') !== false) {
            $title = "Mentioned in Feedback";
            $message = "Admin was mentioned in a feedback message by {$senderName}.";
            foreach ($this->userModel->getAdmins() as $admin) {
                if ((int) $admin['id'] !== $senderId) {
                    $this->notificationModel->create($admin['id'], $title, $message, 'feedback_mention', $feedbackId);
                }
            }
        }

        // Handle @organizer
        if (stripos($text, '@organizer') !== false) {
            $title = "Mentioned in Feedback";
            $message = "Organizer was mentioned in a feedback message by {$senderName}.";
            foreach ($this->userModel->getOrganizers() as $organizer) {
                if ((int) $organizer['id'] !== $senderId) {
                    $this->notificationModel->create($organizer['id'], $title, $message, 'feedback_mention', $feedbackId);
                }
            }
        }

        // Handle @client
        if (stripos($text, '@client') !== false) {
            $feedback = $this->feedbackModel->getById($feedbackId);
            if ($feedback && isset($feedback['client_id'])) {
                if ((int) $feedback['client_id'] !== $senderId) {
                    $title = "Mentioned in Feedback";
                    $message = "Client was mentioned in a feedback message by {$senderName}.";
                    $this->notificationModel->create($feedback['client_id'], $title, $message, 'feedback_mention', $feedbackId);
                }
            }
        }
    }
}
