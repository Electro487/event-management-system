<?php

class AnalyticsService
{
    public function index($start, $end): array
    {
        $eventModel = new Event();
        $bookingModel = new Booking();
        $paymentModel = new Payment();

        // Calculate prior period dynamically for percentage trends
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        $interval = $startDate->diff($endDate)->days + 1;
        
        $priorStart = (clone $startDate)->modify("-{$interval} days")->format('Y-m-d');
        $priorEnd = (clone $startDate)->modify("-1 day")->format('Y-m-d');

        // 1. Core Platform Stats
        $currentRevenue = $bookingModel->getTotalSystemRevenueByDate($start, $end);
        $priorRevenue = $bookingModel->getTotalSystemRevenueByDate($priorStart, $priorEnd);
        $revTrend = $this->calcTrend($currentRevenue, $priorRevenue);
        
        $currentEvents = $eventModel->countAllByDate($start, $end);
        $priorEvents = $eventModel->countAllByDate($priorStart, $priorEnd);
        $eventsTrend = $this->calcTrend($currentEvents, $priorEvents);

        $currentBookings = $bookingModel->countAllByDate($start, $end);
        $priorBookings = $bookingModel->countAllByDate($priorStart, $priorEnd);
        $salesTrend = $this->calcTrend($currentBookings, $priorBookings);

        $successfulBookings = $bookingModel->countSuccessfulBookingsByDate($start, $end);
        $bookingRate = $currentBookings > 0 ? ($successfulBookings / $currentBookings) * 100 : 0;
        
        $priorSuccessfulBookings = $bookingModel->countSuccessfulBookingsByDate($priorStart, $priorEnd);
        $priorBookingRate = $priorBookings > 0 ? ($priorSuccessfulBookings / $priorBookings) * 100 : 0;
        $rateTrend = $this->calcTrend($bookingRate, $priorBookingRate);

        // 2. Top Performing Events (Max 2 as requested)
        $topEvents = $eventModel->getTopPerformingEventsByDate(2, $start, $end);

        // 3. Category Breakdown
        $categoryRevenueData = $eventModel->getRevenueByCategoryByDate($start, $end);
        $allCategoriesList = $eventModel->getAllCategories();
        
        $catMap = [
            'Meetings' => 0,
            'Weddings' => 0,
            'Family Functions' => 0,
            'Concert' => 0,
            'Cultural Events' => 0,
            'Other Events and Programs' => 0
        ];
        foreach($allCategoriesList as $catName) {
            if (!isset($catMap[$catName])) {
                $catMap[$catName] = 0;
            }
        }
        foreach($categoryRevenueData as $cr) {
            $catMap[$cr['category']] = (float)$cr['total_revenue'];
        }
        
        $categoryRevenue = [];
        foreach($catMap as $catName => $amount) {
            $categoryRevenue[] = ['category' => $catName, 'total_revenue' => $amount];
        }

        // 4. Packages Breakdown
        $packages = $bookingModel->getBookingsByPackageTierByDate($start, $end);

        // 5. Revenue Over Time (Graph Data)
        $revenueOverTime = $bookingModel->getRevenueOverTimeByDate($start, $end);

        // 6. Recent Transactions (from payments table)
        $recentTransactions = $this->formatRecentTransactions(
            $paymentModel->getRecentByDateRange($start, $end, 50)
        );

        return [
            'ok' => true,
            'status' => 200,
            'data' => [
                'stats' => [
                    'revenue' => $currentRevenue,
                    'revenue_trend' => $revTrend,
                    'sales' => $currentBookings,
                    'sales_trend' => $salesTrend,
                    'booking_rate' => round($bookingRate, 1),
                    'rate_trend' => $rateTrend,
                    'total_events' => $currentEvents,
                    'events_trend' => $eventsTrend
                ],
                'top_events' => $topEvents,
                'categories' => $categoryRevenue,
                'packages' => $packages,
                'revenue_over_time' => $revenueOverTime,
                'recent_transactions' => $recentTransactions,
            ],
        ];
    }

    private function formatRecentTransactions(array $rows): array
    {
        return array_map(function ($row) {
            $status = $this->mapPaymentStatus($row['status'] ?? '');

            return [
                'id' => 'TX-' . str_pad((string) ($row['id'] ?? ''), 4, '0', STR_PAD_LEFT),
                'client' => $row['client_name'] ?? 'Unknown Client',
                'event' => $this->resolveEventTitle($row),
                'amount' => (float) ($row['amount'] ?? 0),
                'date' => !empty($row['created_at'])
                    ? date('M j, Y', strtotime($row['created_at']))
                    : '—',
                'method' => $this->formatPaymentMethod($row['payment_method'] ?? 'card'),
                'status' => $status['label'],
                'status_class' => $status['class'],
            ];
        }, $rows);
    }

    private function resolveEventTitle(array $row): string
    {
        if (!empty($row['event_title'])) {
            return $row['event_title'];
        }

        if (!empty($row['event_snapshot'])) {
            $snapshot = json_decode($row['event_snapshot'], true);
            if (is_array($snapshot) && !empty($snapshot['title'])) {
                return $snapshot['title'];
            }
        }

        return 'Unknown Event';
    }

    private function formatPaymentMethod(string $method): string
    {
        $normalized = strtolower(str_replace([' ', '-'], '_', trim($method)));
        $labels = [
            'card' => 'Card',
            'cash' => 'Cash',
            'esewa' => 'eSewa',
            'khalti' => 'Khalti',
            'bank_transfer' => 'Bank Transfer',
        ];

        return $labels[$normalized] ?? ucwords(str_replace('_', ' ', $normalized));
    }

    private function mapPaymentStatus(string $status): array
    {
        return match (strtolower($status)) {
            'succeeded' => ['label' => 'Paid', 'class' => 'paid'],
            'failed' => ['label' => 'Failed', 'class' => 'failed'],
            default => ['label' => 'Pending', 'class' => 'pending'],
        };
    }

    private function calcTrend($current, $prior)
    {
        if ($prior == 0) {
            return $current > 0 ? '+100%' : '0%';
        }
        $diff = (($current - $prior) / $prior) * 100;
        $sign = $diff > 0 ? '+' : '';
        return $sign . round($diff, 1) . '%';
    }
}
