<?php

class PaymentApiController
{
    private PaymentService $service;

    public function __construct()
    {
        $this->service = new PaymentService();
    }

    public function checkout(): void
    {
        $this->respond($this->service->checkout($GLOBALS['api_auth_user'] ?? [], Request::body()));
    }

    public function confirm(): void
    {
        $this->respond($this->service->confirm($GLOBALS['api_auth_user'] ?? [], Request::body()));
    }

    public function summary(): void
    {
        $this->respond($this->service->summary($GLOBALS['api_auth_user'] ?? [], (int)Request::param('bookingId', 0)));
    }

    public function history(): void
    {
        $this->respond($this->service->history($GLOBALS['api_auth_user'] ?? [], (int)Request::param('bookingId', 0)));
    }

    private function respond(array $result): void
    {
        if (!($result['ok'] ?? false)) {
            ApiResponse::error($result['message'] ?? 'Request failed.', (int)($result['status'] ?? 400), $result['meta'] ?? [], 'PAYMENT_REQUEST_FAILED');
            return;
        }

        ApiResponse::success($result['data'] ?? [], (int)($result['status'] ?? 200), $result['meta'] ?? []);
    }
}
