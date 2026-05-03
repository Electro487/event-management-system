<?php

class BookingService
{
    private Booking $bookingModel;
    private Event $eventModel;
    private User $userModel;
    private Notification $notificationModel;
    private Payment $paymentModel;
    private Ticket $ticketModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->eventModel = new Event();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
        $this->paymentModel = new Payment();
        $this->ticketModel = new Ticket();
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

        $booking['paid_amount'] = (float)$this->paymentModel->getSucceededTotalByBookingId($id);
        $lastPayment = $this->paymentModel->getByBookingId($id);
        $booking['transaction_id'] = $lastPayment['transaction_id'] ?? null;
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

        // Prepare Snapshots
        $tierKey = (string)($payload['package_tier'] ?? 'basic');
        $allPackages = json_decode($event['packages'] ?? '{}', true);
        $selectedPackage = $allPackages[$tierKey] ?? null;

        $eventSnapshot = [
            'title' => $event['title'],
            'category' => $event['category'],
            'venue_name' => $event['venue_name'],
            'venue_location' => $event['venue_location'],
            'image_path' => $event['image_path']
        ];

        // Handle event_date: for concerts, use event's fixed date if not provided
        $eventDate = (string)($payload['event_date'] ?? '');
        $isConcert = (trim(strtolower($event['category'] ?? '')) === 'concert');

        if ($isConcert) {
            $eventDate = !empty($event['event_date']) ? date('Y-m-d', strtotime($event['event_date'])) : '';
            if (empty($eventDate)) {
                return ['ok' => false, 'status' => 422, 'message' => 'This concert has no date set by the organizer. Please contact support.'];
            }
        } elseif (empty($eventDate)) {
            return ['ok' => false, 'status' => 422, 'message' => 'Event date is required.'];
        }

        $totalAmount = (float)($payload['total_amount'] ?? 0);
        $guestCount = (int)($payload['guest_count'] ?? 0);

        if ($isConcert) {
            // Enforcement: Max 5 tickets
            if ($guestCount > 5) {
                return ['ok' => false, 'status' => 422, 'message' => 'Maximum 5 tickets allowed per booking.'];
            }
            
            // Protection: Cap premium concert prices at 100k
            if ($tierKey === 'premium' && ($totalAmount / $guestCount) > 100000) {
                $totalAmount = 100000 * $guestCount;
            }
        }

        $data = [
            'event_id' => $eventId,
            'event_snapshot' => json_encode($eventSnapshot),
            'client_id' => $authUser['id'],
            'package_tier' => $tierKey,
            'package_snapshot' => json_encode($selectedPackage),
            'event_date' => $eventDate,
            'guest_count' => $guestCount,
            'full_name' => (string)($payload['full_name'] ?? ''),
            'email' => (string)($payload['email'] ?? ''),
            'phone' => (string)($payload['phone'] ?? ''),
            'checkin_time' => (string)($payload['checkin_time'] ?? ''),
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ];

        $bookingId = $this->bookingModel->create($data);
        if (!$bookingId) {
            return ['ok' => false, 'status' => 500, 'message' => 'Failed to create booking.'];
        }

        // Tickets are now generated ONLY after successful payment in PaymentService->confirm 
        // to ensure "payment compulsory for the ticket"

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

    public function cancel(array $authUser, int $id): array
    {
        $booking = $this->bookingModel->getById($id);
        if (!$booking) {
            return ['ok' => false, 'status' => 404, 'message' => 'Booking not found.'];
        }

        // Permission Check
        $isOwner = (int)$booking['client_id'] === (int)$authUser['id'];
        $isOrganizerOfEvent = (int)$booking['organizer_id'] === (int)$authUser['id'] && $authUser['role'] === 'organizer';
        $isAdmin = $authUser['role'] === 'admin';

        if (!$isOwner && !$isOrganizerOfEvent && !$isAdmin) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }

        // Clients can only cancel if not confirmed/paid
        if ($isOwner && !$isAdmin && !$isOrganizerOfEvent) {
            $bStatus = strtolower($booking['status'] ?? 'pending');
            $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');
            if ($bStatus === 'confirmed' && $payStatus !== 'unpaid') {
                return ['ok' => false, 'status' => 409, 'message' => 'Confirmed and paid booking cannot be cancelled by client.'];
            }
        }

        // Perform cancellation
        // Note: we use updateStatus since bModel->cancel is tied to client_id
        $this->bookingModel->updateStatus($id, 'cancelled');
        
        $actorName = $authUser['fullname'] ?? 'A user';
        $message = "{$actorName} has cancelled the booking for '{$booking['event_title']}'.";
        
        // Notify involved parties
        if ($isOwner) {
            $this->notificationModel->create($booking['organizer_id'], 'Booking Cancelled by Client', $message, 'booking_cancel', $id);
        } else {
            $this->notificationModel->create($booking['client_id'], 'Booking Cancelled', "Your booking for '{$booking['event_title']}' was cancelled by the organizer/admin.", 'booking_cancel', $id);
        }
        
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
