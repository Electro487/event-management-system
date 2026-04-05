<?php

class PaymentController
{
    private function checkClientAuth()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
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
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($booking_id);

        // Security: Ensure booking belongs to this client and is unpaid
        if (!$booking || $booking['client_id'] !== $_SESSION['user_id'] || $booking['payment_status'] === 'paid') {
            header('Location: /EventManagementSystem/public/client/events#my-bookings');
            exit;
        }

        // Initialize Stripe
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'npr',
                        'product_data' => [
                            'name' => 'Booking for ' . $booking['event_title'],
                            'description' => $booking['package_tier'] . ' Package - ' . $booking['guest_count'] . ' guests',
                        ],
                        'unit_amount' => (int)($booking['total_amount'] * 0.50 * 100), // 50% Advance in cents/paisa
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => URL_ROOT . '/client/payment/success?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $booking_id,
                'cancel_url' => URL_ROOT . '/client/payment/cancel?booking_id=' . $booking_id,
                'metadata' => [
                    'booking_id' => $booking_id,
                    'client_id' => $_SESSION['user_id'],
                    'payment_type' => 'advance_50'
                ]
            ]);

            // Save session ID for verification later
            // We can keep it in the session or update the booking if we had a field, 
            // but we'll record it in payments table during success.
            
            header("Location: " . $session->url);
            exit;
        } catch (Exception $e) {
            error_log("Stripe Session Error: " . $e->getMessage());
            header('Location: /EventManagementSystem/public/client/events?error=stripe_error');
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
                
                // Double check if already paid to avoid duplicate records
                if ($booking && $booking['payment_status'] === 'unpaid') {
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
                    
                    // 2. Update Booking Status (Set to partially_paid for 50% advance)
                    $bookingModel->updatePaymentStatus($booking_id, 'partially_paid');

                    // 3. Notify Client
                    $clientName = $_SESSION['user_fullname'] ?? $booking['full_name'];
                    $notificationModel->create(
                        $_SESSION['user_id'],
                        'Payment Received',
                        'We have received your 50% advance payment of NPR ' . number_format($session->amount_total / 100, 2) . ' for event: ' . $booking['event_title'] . '. Your booking is now pending organizer approval.',
                        'payment',
                        $booking_id
                    );

                    // 4. Notify Organizer and Admins (with role-based routing)
                    require_once dirname(__DIR__) . '/models/User.php';
                    $userModel = new User();
                    $organizer = $userModel->findById($booking['organizer_id']);
                    $allAdmins = $userModel->getAdmins();
                    
                    $msg = 'A 50% advance payment of NPR ' . number_format($session->amount_total / 100, 2) . ' has been made by ' . $clientName . ' for your event: ' . $booking['event_title'] . '.';

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
                }

                $transaction_id = $session->payment_intent;
                require_once dirname(__DIR__) . '/views/client/payment/success.php';
            } else {
                header('Location: /EventManagementSystem/public/client/payment/cancel?booking_id=' . $booking_id);
                exit;
            }
        } catch (Exception $e) {
            error_log("Stripe Success Verification Error: " . $e->getMessage());
            header('Location: /EventManagementSystem/public/client/events?error=verification_failed');
            exit;
        }
    }

    public function cancel()
    {
        $this->checkClientAuth();
        $booking_id = $_GET['booking_id'] ?? null;
        
        require_once dirname(__DIR__) . '/views/client/payment/cancel.php';
    }
}
