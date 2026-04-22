<?php

class BookingService
{
    private Booking $bookingModel;
    private Event $eventModel;
    private User $userModel;
    private Notification $notificationModel;
    private Payment $paymentModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->eventModel = new Event();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
        $this->paymentModel = new Payment();
    }

    public function list(array $authUser): array
    {
        if ($authUser['role'] === 'client') {
            $items = $this->bookingModel->getByClient($authUser['id']);
        } elseif ($authUser['role'] === 'organizer') {
            $items = $this->bookingModel->getByOrganizer($authUser['id']);
        } else {
            $items = $this->bookingModel->getAll();
        }
        return ['ok' => true, 'status' => 200, 'data' => ['items' => $items]];
    }

    public function detail(array $authUser, int $id): array
    {
        $booking = $this->bookingModel->getById($id);
        if (!$booking) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }

        $forbidden = $authUser['role'] === 'client' && (int)$booking['client_id'] !== (int)$authUser['id'];
        $forbidden = $forbidden || ($authUser['role'] === 'organizer' && (int)$booking['organizer_id'] !== (int)$authUser['id']);
        if ($forbidden) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }

        return ['ok' => true, 'status' => 200, 'data' => ['booking' => $booking]];
    }

    public function create(array $authUser, array $payload): array
    {
        if ($authUser['role'] !== 'client') {
            return ['ok' => false, 'status' => 403, 'message' => 'Only client can create bookings.'];
        }

        $eventId = (int)($payload['event_id'] ?? 0);
        if ($eventId <= 0 || $this->bookingModel->exists($eventId, $authUser['id'])) {
            return ['ok' => false, 'status' => 409, 'message' => 'You have already booked this event.'];
        }

        $event = $this->eventModel->getById($eventId);
        if (!$event || ($event['status'] ?? '') !== 'active') {
            return ['ok' => false, 'status' => 404, 'message' => 'Event not found.'];
        }

        $data = [
            'event_id' => $eventId,
            'client_id' => $authUser['id'],
            'package_tier' => (string)($payload['package_tier'] ?? ''),
            'event_date' => (string)($payload['event_date'] ?? ''),
            'guest_count' => (int)($payload['guest_count'] ?? 0),
            'full_name' => (string)($payload['full_name'] ?? ''),
            'email' => (string)($payload['email'] ?? ''),
            'phone' => (string)($payload['phone'] ?? ''),
            'checkin_time' => (string)($payload['checkin_time'] ?? ''),
            'total_amount' => (float)($payload['total_amount'] ?? 0),
            'status' => 'pending',
        ];

        $bookingId = $this->bookingModel->create($data);
        if (!$bookingId) {
            return ['ok' => false, 'status' => 500, 'message' => 'Failed to create booking.'];
        }

        $clientName = $authUser['fullname'] ?? 'A client';
        $eventTitle = $event['title'] ?? 'an event';
        foreach ($this->userModel->getAdmins() as $admin) {
            $this->notificationModel->create(
                $admin['id'],
                'Booking Received',
                "{$clientName} booked '{$eventTitle}'.",
                'booking',
                $bookingId
            );
        }
        $this->notificationModel->create($event['organizer_id'], 'New Booking for Your Event', "{$clientName} booked '{$eventTitle}'.", 'booking', $bookingId);
        $this->notificationModel->create($authUser['id'], 'Booking Request Received', "Your '{$eventTitle}' booking request has been received.", 'booking', $bookingId);

        return ['ok' => true, 'status' => 201, 'data' => ['booking_id' => (int)$bookingId]];
    }

    public function cancelByClient(array $authUser, int $id): array
    {
        if ($authUser['role'] !== 'client') {
            return ['ok' => false, 'status' => 403, 'message' => 'Only client can cancel via this endpoint.'];
        }

        $booking = $this->bookingModel->getById($id);
        if (!$booking || (int)$booking['client_id'] !== (int)$authUser['id']) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }

        $bStatus = strtolower($booking['status'] ?? 'pending');
        $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');
        if ($bStatus === 'confirmed' && $payStatus !== 'unpaid') {
            return ['ok' => false, 'status' => 409, 'message' => 'Confirmed and paid/partially-paid booking cannot be cancelled.'];
        }

        $this->bookingModel->cancel($id, $authUser['id']);
        $message = ($authUser['fullname'] ?? 'A user') . " has cancelled their booking for '{$booking['event_title']}'.";
        $this->notificationModel->create($booking['organizer_id'], 'Booking Cancelled', $message, 'booking_cancel', $id);
        foreach ($this->userModel->getAdmins() as $admin) {
            $this->notificationModel->create($admin['id'], 'Booking Cancelled', $message, 'booking_cancel', $id);
        }

        return ['ok' => true, 'status' => 200, 'data' => ['cancelled' => true]];
    }

    public function approve(array $authUser, int $id): array
    {
        if (!in_array($authUser['role'], ['admin', 'organizer'], true)) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }
        $booking = $this->bookingModel->getById($id);
        if (!$booking) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }
        if ($authUser['role'] === 'organizer' && (int)$booking['organizer_id'] !== (int)$authUser['id']) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }
        $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');
        if (!in_array($payStatus, ['paid', 'partially_paid'], true)) {
            return ['ok' => false, 'status' => 409, 'message' => 'Booking can be approved only if payment status is partially_paid or paid.'];
        }

        $this->bookingModel->updateStatus($id, 'confirmed');
        $this->notificationModel->create($booking['client_id'], 'Booking Confirmed', "Your booking for '{$booking['event_title']}' has been confirmed.", 'booking_approve', $id);
        return ['ok' => true, 'status' => 200, 'data' => ['approved' => true]];
    }

    public function markPaid(array $authUser, int $id): array
    {
        if (!in_array($authUser['role'], ['admin', 'organizer'], true)) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }
        $booking = $this->bookingModel->getById($id);
        if (!$booking) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }
        if ($authUser['role'] === 'organizer' && (int)$booking['organizer_id'] !== (int)$authUser['id']) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }
        if (strtolower($booking['payment_status'] ?? 'unpaid') !== 'partially_paid') {
            return ['ok' => false, 'status' => 409, 'message' => 'Only partially_paid booking can be marked paid.'];
        }

        $paidSoFar = $this->paymentModel->getSucceededTotalByBookingId($id);
        $cashAmount = max(0, (float)$booking['total_amount'] - $paidSoFar);
        if ($cashAmount > 0.009) {
            $this->paymentModel->create([
                'booking_id' => $id,
                'client_id' => $booking['client_id'],
                'transaction_id' => str_replace('.', '_', uniqid('cash_api_' . $id . '_', true)),
                'amount' => $cashAmount,
                'payment_method' => 'cash',
                'status' => 'succeeded',
            ]);
        }

        $this->bookingModel->updatePaymentStatus($id, 'paid');
        $this->notificationModel->create($booking['client_id'], 'Payment Fully Paid (Cash)', "Your payment for '{$booking['event_title']}' has been recorded as Fully Paid (Cash).", 'payment', $id);

        return ['ok' => true, 'status' => 200, 'data' => ['paid' => true]];
    }
}
