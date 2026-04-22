<?php

class BookingApiController
{
    private BookingService $service;

    public function __construct()
    {
        $this->service = new BookingService();
    }

    public function index(): void
    {
        $this->respond($this->service->list($GLOBALS['api_auth_user'] ?? []));
    }

    public function show(): void
    {
        $this->respond($this->service->detail($GLOBALS['api_auth_user'] ?? [], (int)Request::param('id', 0)));
    }

    public function store(): void
    {
        $this->respond($this->service->create($GLOBALS['api_auth_user'] ?? [], Request::body()));
    }

    public function cancel(): void
    {
        $this->respond($this->service->cancelByClient($GLOBALS['api_auth_user'] ?? [], (int)Request::param('id', 0)));
    }

    public function approve(): void
    {
        $this->respond($this->service->approve($GLOBALS['api_auth_user'] ?? [], (int)Request::param('id', 0)));
    }

    public function markPaid(): void
    {
        $this->respond($this->service->markPaid($GLOBALS['api_auth_user'] ?? [], (int)Request::param('id', 0)));
    }

    private function respond(array $result): void
    {
        if (!($result['ok'] ?? false)) {
            ApiResponse::error($result['message'] ?? 'Request failed.', (int)($result['status'] ?? 400), [], 'BOOKING_REQUEST_FAILED');
            return;
        }
        ApiResponse::success($result['data'] ?? [], (int)($result['status'] ?? 200));
    }
}
