<?php

class PromoCodeApiController
{
    private PromoCode $promoCodeModel;
    private User $userModel;
    private Notification $notificationModel;

    public function __construct()
    {
        $this->promoCodeModel = new PromoCode();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }

    public function index()
    {
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user || !in_array($user['role'], ['admin', 'organizer'])) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $codes = $this->promoCodeModel->getAll();
        return ApiResponse::success($codes);
    }

    public function store()
    {
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user || !in_array($user['role'], ['admin', 'organizer'])) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $body = Request::body();
        $data = [
            'code' => $body['code'] ?? '',
            'discount_percentage' => (float)($body['discount_percentage'] ?? 0),
            'expires_at' => $body['expires_at'] ?? '',
            'created_by' => $user['id'],
            'usage_limit' => (isset($body['usage_limit']) && $body['usage_limit'] !== '') ? (int)$body['usage_limit'] : null
        ];

        if (empty($data['code']) || $data['discount_percentage'] <= 0 || empty($data['expires_at'])) {
            return ApiResponse::error('Invalid input data', 422);
        }

        if ($this->promoCodeModel->create($data)) {
            // Notify all clients
            $clients = $this->userModel->getAllByRole('client');
            $message = "New Promo Code: " . strtoupper($data['code']) . "! Get " . $data['discount_percentage'] . "% off your next booking. Valid until " . date('F j, Y', strtotime($data['expires_at'])) . ".";
            
            foreach ($clients as $client) {
                $this->notificationModel->create(
                    $client['id'],
                    'Special Offer: New Promo Code!',
                    $message,
                    'promo_code'
                );
            }

            return ApiResponse::success(['message' => 'Promo code created and users notified.'], 201);
        }

        return ApiResponse::error('Failed to create promo code');
    }

    public function validate()
    {
        $body = Request::body();
        $code = $body['code'] ?? '';

        if (empty($code)) {
            return ApiResponse::error('Promo code is required', 422);
        }

        $promo = $this->promoCodeModel->getByCode($code);
        if ($promo) {
            return ApiResponse::success([
                'valid' => true,
                'discount_percentage' => (float)$promo['discount_percentage']
            ]);
        }

        return ApiResponse::error('Invalid or expired promo code', 404);
    }

    public function delete()
    {
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user || !in_array($user['role'], ['admin', 'organizer'])) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $id = (int)Request::param('id');
        if ($this->promoCodeModel->delete($id)) {
            return ApiResponse::success(['message' => 'Promo code deleted.']);
        }

        return ApiResponse::error('Failed to delete promo code');
    }
}
