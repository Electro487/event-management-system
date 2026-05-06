<?php

class AnalyticsService
{
    public function index($start, $end): array
    {
        $eventModel = new Event();
        $bookingModel = new Booking();

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
                // Static transactions for now as requested
                'recent_transactions' => [
                    ['id' => 'TX-8822', 'client' => 'Pratik Kumar', 'event' => 'Global Dev Summit', 'amount' => 4500, 'date' => 'Oct 24, 2023', 'method' => 'eSewa', 'status' => 'Paid'],
                    ['id' => 'TX-8819', 'client' => 'Ananya Sharma', 'event' => 'Winter Fest Ed.', 'amount' => 12000, 'date' => 'Oct 23, 2023', 'method' => 'Bank Transfer', 'status' => 'Pending'],
                    ['id' => 'TX-8815', 'client' => 'Vikram Singh', 'event' => 'Startup Expo 24', 'amount' => 2100, 'date' => 'Oct 22, 2023', 'method' => 'Khalti', 'status' => 'Paid'],
                    ['id' => 'TX-8812', 'client' => 'Sarah Christina', 'event' => 'Music Night Live', 'amount' => 1500, 'date' => 'Oct 20, 2023', 'method' => 'Card', 'status' => 'Failed'],
                ]
            ],
        ];
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
