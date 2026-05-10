<?php

class AnalyticsApiController
{
    private AnalyticsService $service;

    public function __construct()
    {
        $this->service = new AnalyticsService();
    }

    public function index(): void
    {
        $this->authorize('admin');
        
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');

        $this->respond($this->service->index($start, $end));
    }

    private function authorize(string $role): void
    {
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        if (($authUser['role'] ?? null) !== $role) {
            ApiResponse::error('Forbidden.', 403, [], 'FORBIDDEN');
            exit;
        }
    }

    private function respond(array $result): void
    {
        if (!($result['ok'] ?? false)) {
            ApiResponse::error($result['message'] ?? 'Request failed.', (int)($result['status'] ?? 400), [], 'ANALYTICS_REQUEST_FAILED');
            return;
        }
        ApiResponse::success($result['data'] ?? [], (int)($result['status'] ?? 200));
    }
}
