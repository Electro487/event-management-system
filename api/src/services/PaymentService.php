<?php

class PaymentService
{
    private Booking $bookingModel;
    private Payment $paymentModel;
    private Notification $notificationModel;
    private User $userModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->paymentModel = new Payment();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }

    public function checkout(array $authUser, array $payload): array
    {
        if (($authUser['role'] ?? null) !== 'client') {
            return ['ok' => false, 'status' => 403, 'message' => 'Only clients can create payment checkout sessions.'];
        }

        $bookingId = (int)($payload['booking_id'] ?? 0);
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking || (int)$booking['client_id'] !== (int)$authUser['id']) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }
        if (($booking['payment_status'] ?? '') === 'paid') {
            return ['ok' => false, 'status' => 409, 'message' => 'Booking payment already completed.'];
        }

        $advanceTarget = (float)$booking['total_amount'] * 0.50;
        $paidAdvance = $this->paymentModel->getSucceededTotalByBookingId($bookingId);
        $remainingAdvance = max(0, $advanceTarget - $paidAdvance);

        // Stripe minimum charge for NPR is 50. If remaining is less than that, we can't charge it.
        // We consider it complete if the remaining balance is negligible (< 50 NPR) and they've already paid something.
        // Treat as complete if remaining balance is negligible (< 50 NPR)
        // Treat as complete if remaining balance is negligible (< 50 NPR)
        if ($remainingAdvance < 50 && $paidAdvance > 0) {
            return ['ok' => false, 'status' => 409, 'message' => 'Advance target already completed. Any tiny remaining balance will be settled offline on the event day.'];
        }

        if ($remainingAdvance <= 0.009) {
            return ['ok' => false, 'status' => 409, 'message' => 'Advance target already completed.'];
        }

        $installmentAmount = min($remainingAdvance, 999999.99);

        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'npr',
                        'product_data' => [
                            'name' => 'Booking for ' . $booking['event_title'],
                            'description' => $booking['package_tier'] . ' Package - Installment toward 50% advance',
                        ],
                        'unit_amount' => (int)round($installmentAmount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => URL_ROOT . '/client/payment/success?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $bookingId,
                'cancel_url' => URL_ROOT . '/client/payment/cancel?booking_id=' . $bookingId,
                'metadata' => [
                    'booking_id' => (string)$bookingId,
                    'client_id' => (string)$authUser['id'],
                    'payment_type' => 'advance_installment',
                ],
            ]);
        } catch (Throwable $e) {
            error_log("Stripe Checkout Error: " . $e->getMessage());
            return ['ok' => false, 'status' => 502, 'message' => 'Unable to create Stripe checkout session.', 'meta' => ['reason' => $e->getMessage()]];
        }

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'session_id' => $session->id,
                'checkout_url' => $session->url,
                'booking_id' => $bookingId,
                'installment_amount' => $installmentAmount,
            ],
        ];
    }

    public function confirm(array $authUser, array $payload): array
    {
        if (($authUser['role'] ?? null) !== 'client') {
            return ['ok' => false, 'status' => 403, 'message' => 'Only clients can confirm Stripe payments.'];
        }

        $sessionId = trim((string)($payload['session_id'] ?? ''));
        $bookingId = (int)($payload['booking_id'] ?? 0);
        if ($sessionId === '' || $bookingId <= 0) {
            return ['ok' => false, 'status' => 422, 'message' => 'session_id and booking_id are required.'];
        }

        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking || (int)$booking['client_id'] !== (int)$authUser['id']) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }

        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
        } catch (Throwable $e) {
            return ['ok' => false, 'status' => 502, 'message' => 'Stripe verification failed.', 'meta' => ['reason' => $e->getMessage()]];
        }

        if (($session->payment_status ?? '') !== 'paid') {
            return ['ok' => false, 'status' => 409, 'message' => 'Payment is not marked as paid by Stripe.'];
        }

        // Idempotent confirmation: skip duplicate insert by session ID.
        if (!$this->paymentModel->existsByStripeSessionId($sessionId)) {
            $this->paymentModel->create([
                'booking_id' => $bookingId,
                'client_id' => $authUser['id'],
                'transaction_id' => (string)$session->payment_intent,
                'amount' => ((float)$session->amount_total) / 100,
                'payment_method' => 'card',
                'status' => 'succeeded',
                'stripe_session_id' => $sessionId,
            ]);

            $advanceTarget = (float)$booking['total_amount'] * 0.50;
            $paidAdvance = $this->paymentModel->getSucceededTotalByBookingId($bookingId);
            if ($paidAdvance > 0) {
                $this->bookingModel->updatePaymentStatus($bookingId, 'partially_paid');
            }

            $remainingAdvance = max(0, $advanceTarget - $paidAdvance);
            $amount = ((float)$session->amount_total) / 100;

            $this->notificationModel->create(
                $authUser['id'],
                'Payment Received',
                'We have received an advance installment of NPR ' . number_format($amount, 2) . ' for event: ' . $booking['event_title'] . '. Remaining online advance: NPR ' . number_format($remainingAdvance, 2) . '.',
                'payment',
                $bookingId
            );

            $msg = 'An advance installment of NPR ' . number_format($amount, 2) . ' has been made by ' . ($authUser['fullname'] ?? $booking['full_name']) . ' for event: ' . $booking['event_title'] . '.';
            $organizer = $this->userModel->findById($booking['organizer_id']);
            if ($organizer && $organizer['role'] === 'organizer') {
                $this->notificationModel->create($booking['organizer_id'], 'New Advance Payment', $msg, 'payment_alert', $bookingId);
            }
            foreach ($this->userModel->getAdmins() as $admin) {
                $this->notificationModel->create($admin['id'], 'New Advance Payment', $msg, 'payment_alert', $bookingId);
            }
        }

        return $this->summary($authUser, $bookingId);
    }

    public function summary(array $authUser, int $bookingId): array
    {
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }
        if (!$this->canAccessBooking($authUser, $booking)) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }

        $advanceTarget = (float)$booking['total_amount'] * 0.50;
        $paidAdvance = $this->paymentModel->getSucceededTotalByBookingId($bookingId);
        $remainingAdvance = max(0, $advanceTarget - $paidAdvance);

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'booking_id' => $bookingId,
                'total_amount' => (float)$booking['total_amount'],
                'advance_target' => $advanceTarget,
                'paid_advance' => $paidAdvance,
                'remaining_advance' => $remainingAdvance,
                'is_advance_complete' => ($remainingAdvance <= 0.009) || ($remainingAdvance < 50 && $paidAdvance > 0),
                'next_installment_amount' => min($remainingAdvance, 999999.99),
            ],
        ];
    }

    public function history(array $authUser, int $bookingId): array
    {
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }
        if (!$this->canAccessBooking($authUser, $booking)) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }

        $pdo = (new Database())->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM payments WHERE booking_id = :booking_id ORDER BY created_at DESC');
        $stmt->bindParam(':booking_id', $bookingId);
        $stmt->execute();
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['ok' => true, 'status' => 200, 'data' => ['booking_id' => $bookingId, 'items' => $payments]];
    }

    private function canAccessBooking(array $authUser, array $booking): bool
    {
        $role = $authUser['role'] ?? null;
        if ($role === 'admin') {
            return true;
        }
        if ($role === 'organizer') {
            return (int)$booking['organizer_id'] === (int)$authUser['id'];
        }
        if ($role === 'client') {
            return (int)$booking['client_id'] === (int)$authUser['id'];
        }
        return false;
    }
}
