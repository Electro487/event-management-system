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
                'recent_bookings' => $bookingModel->getRecent(5),
                'upcoming_events' => $eventModel->getUpcoming(5),
            ],
        ];
    }

    public function organizer(int $organizerId): array
    {
        $eventModel = new Event();
        $bookingModel = new Booking();

        $recentBookings = $bookingModel->getRecentByOrganizer($organizerId, 5);
        $today = date('Y-m-d');
        foreach ($recentBookings as &$b) {
            $dateStr = $b['event_date'] ?? null;
            $isPast = ($dateStr && $dateStr < $today);
            $displayStatus = strtolower($b['status']);
            if ($displayStatus === 'confirmed' && $isPast) {
                $displayStatus = 'completed';
            }
            $b['display_status'] = $displayStatus;
        }
        unset($b);

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'total_events' => $eventModel->getTotalEvents($organizerId),
                'total_bookings' => $bookingModel->countByOrganizer($organizerId),
                'pending_requests' => $bookingModel->countByStatusForOrganizer($organizerId, 'pending'),
                'revenue' => $bookingModel->getRevenueByOrganizer($organizerId),
                'status_summary' => [
                    'confirmed' => $bookingModel->countByStatusForOrganizer($organizerId, 'confirmed'),
                    'pending' => $bookingModel->countByStatusForOrganizer($organizerId, 'pending'),
                    'cancelled' => $bookingModel->countByStatusForOrganizer($organizerId, 'cancelled')
                ],
                'recent_bookings' => $recentBookings,
                'upcoming_events' => $eventModel->getUpcomingEvents($organizerId, 5),
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
