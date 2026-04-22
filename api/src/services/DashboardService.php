<?php

class DashboardService
{
    public function admin(): array
    {
        $eventModel = new Event();
        $bookingModel = new Booking();
        $userModel = new User();

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'total_events' => $eventModel->countAll(),
                'total_bookings' => $bookingModel->countAll(),
                'total_users' => $userModel->countAll(),
                'pending_requests' => $bookingModel->countByStatus('pending'),
                'revenue' => $bookingModel->getTotalSystemRevenue(),
            ],
        ];
    }

    public function organizer(int $organizerId): array
    {
        $eventModel = new Event();
        $bookingModel = new Booking();

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'total_events' => $eventModel->getTotalEvents($organizerId),
                'total_bookings' => $bookingModel->countByOrganizer($organizerId),
                'pending_requests' => $bookingModel->countByStatusForOrganizer($organizerId, 'pending'),
                'revenue' => $bookingModel->getRevenueByOrganizer($organizerId),
            ],
        ];
    }

    public function client(int $clientId): array
    {
        $bookingModel = new Booking();
        $bookings = $bookingModel->getByClient($clientId);

        $upcoming = 0;
        $completed = 0;
        $pending = 0;
        $today = date('Y-m-d');
        foreach ($bookings as $b) {
            $dateStr = $b['event_date'] ?: ($b['event_start_date'] ?? '9999-12-31');
            $displayStatus = strtolower($b['status']);
            if ($displayStatus === 'confirmed' && $dateStr < $today) {
                $displayStatus = 'completed';
            }
            if (in_array($displayStatus, ['pending', 'confirmed'], true)) {
                $upcoming++;
            }
            if ($displayStatus === 'completed') {
                $completed++;
            }
            if ($displayStatus === 'pending') {
                $pending++;
            }
        }

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'total_bookings' => count($bookings),
                'upcoming' => $upcoming,
                'completed' => $completed,
                'pending' => $pending,
            ],
        ];
    }
}
