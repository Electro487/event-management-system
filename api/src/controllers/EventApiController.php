<?php

class EventApiController
{
    private EventService $service;

    public function __construct()
    {
        $this->service = new EventService();
    }

    public function index(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        $result = $this->service->list(
            $authUser,
            Request::input('category'),
            Request::input('search'),
            max(1, (int)Request::input('page', 1)),
            max(1, min(100, (int)Request::input('limit', 20)))
        );
        $this->respond($result);
    }

    public function show(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        $result = $this->service->detail($authUser, (int)Request::param('id', 0));
        $this->respond($result);
    }

    public function store(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        try {
            $result = $this->service->create($authUser, Request::body());
            $this->respond($result);
        } catch (Throwable $e) {
            ApiResponse::error($e->getMessage(), 422, [], 'EVENT_VALIDATION_FAILED');
        }
    }

    public function update(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        try {
            $result = $this->service->update($authUser, (int)Request::param('id', 0), Request::body());
            $this->respond($result);
        } catch (Throwable $e) {
            ApiResponse::error($e->getMessage(), 422, [], 'EVENT_VALIDATION_FAILED');
        }
    }

    public function delete(): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        $result = $this->service->delete($authUser, (int)Request::param('id', 0));
        $this->respond($result);
    }

    private function respond(array $result): void
    {
        if (!($result['ok'] ?? false)) {
            ApiResponse::error($result['message'] ?? 'Request failed.', (int)($result['status'] ?? 400), [], 'EVENT_REQUEST_FAILED');
            return;
        }
        ApiResponse::success($result['data'] ?? [], (int)($result['status'] ?? 200));
    }
}
