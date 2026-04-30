<?php

class DashboardApiController
{
    private DashboardService $service;

    public function __construct()
    {
        $this->service = new DashboardService();
    }

    public function admin(): void
    {
        $this->authorize('admin');
        $this->respond($this->service->admin());
    }

    public function organizer(): void
    {
        $this->authorize('organizer');
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        $this->respond($this->service->organizer((int)$authUser['id']));
    }

    public function client(): void
    {
        $this->authorize('client');
        $authUser = $GLOBALS['api_auth_user'] ?? [];
        $this->respond($this->service->client((int)$authUser['id']));
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
            ApiResponse::error($result['message'] ?? 'Request failed.', (int)($result['status'] ?? 400), [], 'DASHBOARD_REQUEST_FAILED');
            return;
        }
        ApiResponse::success($result['data'] ?? [], (int)($result['status'] ?? 200));
    }
}
