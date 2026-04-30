<?php

class PaymentController
{
    private function checkClientAuth()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        require_once dirname(__DIR__) . '/models/User.php';
        $userModel = new User();
        $currentUser = $userModel->findById($_SESSION['user_id']);

        if (!$currentUser || !empty($currentUser['is_blocked'])) {
            session_destroy();
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        $_SESSION['user_role'] = $currentUser['role'];
        $role = $_SESSION['user_role'];

        if ($role === 'admin') {
            header('Location: /EventManagementSystem/public/admin/dashboard');
            exit;
        }

        if ($role === 'organizer') {
            header('Location: /EventManagementSystem/public/organizer/dashboard');
            exit;
        }

        if ($role !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }
    }

    public function checkout()
    {
        $this->checkClientAuth();

        $booking_id = $_GET['booking_id'] ?? null;
        if (!$booking_id) {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Booking.php';
        require_once dirname(__DIR__) . '/models/Payment.php';
        $bookingModel = new Booking();
        $paymentModel = new Payment();
        $booking = $bookingModel->getById($booking_id);

        // Security: Ensure booking belongs to this client and is not fully settled
        if (!$booking || $booking['client_id'] !== $_SESSION['user_id'] || $booking['payment_status'] === 'paid') {
            header('Location: /EventManagementSystem/public/client/events#my-bookings');
            exit;
        }

        // Initialize Stripe
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $advanceTarget = (float) $booking['total_amount'] * 0.50;
        $paidAdvance = $paymentModel->getSucceededTotalByBookingId($booking_id);
        $remainingAdvance = max(0, $advanceTarget - $paidAdvance);
        $maxStripeCheckoutNpr = 999999.99;

        // Stripe minimum charge for NPR is 50.
        if ($remainingAdvance < 50 && $paidAdvance > 0) {
            header('Location: /EventManagementSystem/public/client/bookings/view?id=' . urlencode((string) $booking_id));
            exit;
        }

        if ($remainingAdvance <= 0.009) {
            header('Location: /EventManagementSystem/public/client/bookings/view?id=' . urlencode((string) $booking_id));
            exit;
        }

        $installmentAmount = min($remainingAdvance, $maxStripeCheckoutNpr);

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'npr',
                        'product_data' => [
                            'name' => 'Booking for ' . $booking['event_title'],
                            'description' => $booking['package_tier'] . ' Package - Installment toward 50% advance',
                        ],
                        'unit_amount' => (int) round($installmentAmount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => URL_ROOT . '/client/payment/success?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $booking_id,
                'cancel_url' => URL_ROOT . '/client/payment/cancel?booking_id=' . $booking_id,
                'metadata' => [
                    'booking_id' => $booking_id,
                    'client_id' => $_SESSION['user_id'],
                    'payment_type' => 'advance_installment',
                    'advance_target' => number_format($advanceTarget, 2, '.', ''),
                    'paid_advance_before' => number_format($paidAdvance, 2, '.', ''),
                    'remaining_advance_before' => number_format($remainingAdvance, 2, '.', '')
                ]
            ]);

            // Save session ID for verification later
            // We can keep it in the session or update the booking if we had a field, 
            // but we'll record it in payments table during success.

            header("Location: " . $session->url);
            exit;
        } catch (Exception $e) {
            error_log("Stripe Session Error: " . $e->getMessage());
            $reason = 'stripe_error';
            if (stripos($e->getMessage(), 'no more than') !== false) {
                $reason = 'amount_limit';
            }
            header('Location: /EventManagementSystem/public/client/payment/error?booking_id=' . urlencode((string) $booking_id) . '&reason=' . urlencode($reason));
            exit;
        }
    }

    // Process is no longer needed for real Stripe Checkout as Stripe handles the form
    public function process()
    {
        header('Location: /EventManagementSystem/public/client/events');
        exit;
    }

    public function success()
    {
        $this->checkClientAuth();

        $session_id = $_GET['session_id'] ?? null;
        $booking_id = $_GET['booking_id'] ?? null;

        if (!$session_id || !$booking_id) {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }

        // Initialize Stripe
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        try {
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($session->payment_status === 'paid') {
                require_once dirname(__DIR__) . '/models/Booking.php';
                require_once dirname(__DIR__) . '/models/Payment.php';
                require_once dirname(__DIR__) . '/models/Event.php';
                require_once dirname(__DIR__) . '/models/Notification.php';

                $bookingModel = new Booking();
                $paymentModel = new Payment();
                $notificationModel = new Notification();

                $booking = $bookingModel->getById($booking_id);

                if (!$booking || (int) $booking['client_id'] !== (int) $_SESSION['user_id']) {
                    header('Location: /EventManagementSystem/public/client/events#my-bookings');
                    exit;
                }

                if ($booking && (int) $booking['client_id'] === (int) $_SESSION['user_id']) {
                    if (!$paymentModel->existsByStripeSessionId($session_id)) {
                        // 1. Create Payment Record
                        $paymentData = [
                            'booking_id' => $booking_id,
                            'client_id' => $_SESSION['user_id'],
                            'transaction_id' => $session->payment_intent,
                            'amount' => $session->amount_total / 100,
                            'payment_method' => 'card',
                            'status' => 'succeeded',
                            'stripe_session_id' => $session_id
                        ];

                        $paymentModel->create($paymentData);

                        $advanceTarget = (float) $booking['total_amount'] * 0.50;
                        $paidAdvance = $paymentModel->getSucceededTotalByBookingId($booking_id);
                        $remainingAdvance = max(0, $advanceTarget - $paidAdvance);

                        // 2. Update booking payment status for progressive advance collection
                        if ($paidAdvance > 0) {
                            $bookingModel->updatePaymentStatus($booking_id, 'partially_paid');
                        }

                        // 3. Notify Client
                        $clientName = $_SESSION['user_fullname'] ?? $booking['full_name'];
                        $notificationModel->create(
                            $_SESSION['user_id'],
                            'Payment Received',
                            'We have received an advance installment of NPR ' . number_format($session->amount_total / 100, 2) . ' for event: ' . $booking['event_title'] . '. Remaining online advance: NPR ' . number_format($remainingAdvance, 2) . '.',
                            'payment',
                            $booking_id
                        );

                        // 4. Notify Organizer and Admins (with role-based routing)
                        require_once dirname(__DIR__) . '/models/User.php';
                        $userModel = new User();
                        $organizer = $userModel->findById($booking['organizer_id']);
                        $allAdmins = $userModel->getAdmins();

                        $msg = 'An advance installment of NPR ' . number_format($session->amount_total / 100, 2) . ' has been made by ' . $clientName . ' for event: ' . $booking['event_title'] . '. Remaining online advance: NPR ' . number_format($remainingAdvance, 2) . '.';

                        if ($organizer && $organizer['role'] === 'organizer') {
                            // Notify the Organizer
                            $notificationModel->create($booking['organizer_id'], 'New Advance Payment', $msg, 'payment_alert', $booking_id);

                            // Also notify all Admins
                            foreach ($allAdmins as $admin) {
                                $notificationModel->create($admin['id'], 'New Advance Payment', $msg, 'payment_alert', $booking_id);
                            }
                        } else {
                            // Event created by Admin: Notify only Admins
                            foreach ($allAdmins as $admin) {
                                $notificationModel->create($admin['id'], 'New Advance Payment', $msg, 'payment_alert', $booking_id);
                            }
                        }

                        // 5. Update any existing "Booking Received" notifications with installment progress.
                        $adminTitle = "Booking Received";
                        $clientName = $_SESSION['user_fullname'] ?? $booking['full_name'];
                        $eventTitle = $booking['event_title'];
                        $pTier = $booking['package_tier'];
                        $eDate = $booking['event_date'];
                        $cTime = !empty($booking['checkin_time']) ? $booking['checkin_time'] : '10:00 AM';
                        if (preg_match('/^\d{2}:\d{2}$/', $cTime)) {
                            $cTime = date('h:i A', strtotime($cTime));
                        }

                        $updatedAdminMsg = "{$clientName} booked '{$eventTitle}'.\nPackage: {$pTier}\nDate: {$eDate}, Check-in: {$cTime}\nAdvance Paid Online: NPR " . number_format($paidAdvance, 2) . " / NPR " . number_format($advanceTarget, 2);
                        $notificationModel->updateMessageByRelatedIdAndTitle($booking_id, 'booking', $adminTitle, $updatedAdminMsg);
                    }
                }

                $advanceTarget = (float) $booking['total_amount'] * 0.50;
                $paidAdvance = $paymentModel->getSucceededTotalByBookingId($booking_id);
                $remainingAdvance = max(0, $advanceTarget - $paidAdvance);
                $maxStripeCheckoutNpr = 999999.99;
                $nextInstallmentAmount = min($remainingAdvance, $maxStripeCheckoutNpr);
                $isAdvanceComplete = ($remainingAdvance <= 0.009) || ($remainingAdvance < 50 && $paidAdvance > 0);

                $transaction_id = $session->payment_intent;
                require_once dirname(__DIR__) . '/views/client/payment/success.php';
            } else {
                header('Location: /EventManagementSystem/public/client/payment/cancel?booking_id=' . $booking_id);
                exit;
            }
        } catch (Exception $e) {
            error_log("Stripe Success Verification Error: " . $e->getMessage());
            header('Location: /EventManagementSystem/public/client/payment/error?booking_id=' . urlencode((string) $booking_id) . '&reason=verification_failed');
            exit;
        }
    }

    public function cancel()
    {
        $this->checkClientAuth();
        $booking_id = $_GET['booking_id'] ?? null;

        require_once dirname(__DIR__) . '/views/client/payment/cancel.php';
    }

    public function error()
    {
        $this->checkClientAuth();

        $booking_id = $_GET['booking_id'] ?? null;
        $reason = $_GET['reason'] ?? 'stripe_error';

        if (!$booking_id) {
            header('Location: /EventManagementSystem/public/client/events#my-bookings');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($booking_id);

        if (!$booking || (int) $booking['client_id'] !== (int) $_SESSION['user_id']) {
            header('Location: /EventManagementSystem/public/client/events#my-bookings');
            exit;
        }

        require_once dirname(__DIR__) . '/views/client/payment/error.php';
    }
}
