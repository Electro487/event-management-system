<?php

class NotificationApiController
{
    private NotificationService $service;

    public function __construct()
    {
        $this->service = new NotificationService();
    }

    public function latest(): void
    {
        $this->respond($this->service->latest($GLOBALS['api_auth_user'] ?? []));
    }

    public function index(): void
    {
        $this->respond($this->service->list($GLOBALS['api_auth_user'] ?? [], Request::input('type')));
    }

    public function counts(): void
    {
        $this->respond($this->service->counts($GLOBALS['api_auth_user'] ?? []));
    }

    public function read(): void
    {
        $this->respond($this->service->markRead($GLOBALS['api_auth_user'] ?? [], (int)Request::param('id', 0)));
    }

    public function unread(): void
    {
        $this->respond($this->service->markUnread($GLOBALS['api_auth_user'] ?? [], (int)Request::param('id', 0)));
    }

    public function markAllRead(): void
    {
        $this->respond($this->service->markAllRead($GLOBALS['api_auth_user'] ?? []));
    }

    public function markAllUnread(): void
    {
        $this->respond($this->service->markAllUnread($GLOBALS['api_auth_user'] ?? []));
    }

    public function deleteOne(): void
    {
        $this->respond($this->service->deleteOne($GLOBALS['api_auth_user'] ?? [], (int)Request::param('id', 0)));
    }

    public function deleteAll(): void
    {
        $this->respond($this->service->deleteAll($GLOBALS['api_auth_user'] ?? []));
    }

    private function respond(array $result): void
    {
        if (!($result['ok'] ?? false)) {
            ApiResponse::error($result['message'] ?? 'Request failed.', (int)($result['status'] ?? 400), $result['meta'] ?? [], 'NOTIFICATION_REQUEST_FAILED');
            return;
        }

        ApiResponse::success($result['data'] ?? [], (int)($result['status'] ?? 200), $result['meta'] ?? []);
    }
}
