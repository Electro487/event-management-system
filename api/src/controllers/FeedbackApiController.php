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
        
        $payload = [
            'success' => true,
            'data' => $result['data'] ?? [],
        ];
        
        if (isset($result['pagination'])) {
            $payload['pagination'] = $result['pagination'];
        }

        ApiResponse::json($payload, (int) ($result['status'] ?? 200));
    }

    public function list(): void
    {
        $rating = $_GET['rating'] ?? null;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
        
        $result = $this->feedbackService->getAll($rating ? (int) $rating : null, $page, $limit);
        $this->respond($result);
    }

    public function myFeedback(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        if (empty($authUser)) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
        
        $result = $this->feedbackService->getByClient((int) $authUser['id'], $page, $limit);
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
