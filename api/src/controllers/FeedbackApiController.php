<?php

class FeedbackApiController
{
    private FeedbackService $feedbackService;

    public function __construct()
    {
        $this->feedbackService = new FeedbackService();
    }

    private function respond(array $result): void
    {
        if (!($result['ok'] ?? false)) {
            ApiResponse::error($result['message'] ?? 'Request failed.', (int) ($result['status'] ?? 400));
            return;
        }
        ApiResponse::success($result['data'] ?? [], (int) ($result['status'] ?? 200));
    }

    public function list(): void
    {
        $rating = $_GET['rating'] ?? null;
        $result = $this->feedbackService->getAll($rating ? (int) $rating : null);
        $this->respond($result);
    }

    public function myFeedback(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        if (empty($authUser)) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }
        $result = $this->feedbackService->getByClient((int) $authUser['id']);
        $this->respond($result);
    }

    public function create(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        if (empty($authUser)) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }
        $data = Request::body();
        $result = $this->feedbackService->create($authUser, $data);
        $this->respond($result);
    }

    public function reply(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        if (empty($authUser)) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }
        $data = Request::body();
        $result = $this->feedbackService->addReply($authUser, $data);
        $this->respond($result);
    }

    public function update(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        if (empty($authUser)) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }
        $data = Request::body();
        $result = $this->feedbackService->updateFeedback($authUser, $data);
        $this->respond($result);
    }

    public function updateReply(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        if (empty($authUser)) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }
        $data = Request::body();
        $result = $this->feedbackService->updateReply($authUser, $data);
        $this->respond($result);
    }

    public function stats(): void
    {
        $result = $this->feedbackService->getStats();
        $this->respond($result);
    }
}
