<?php
require_once dirname(__DIR__) . '/config/database.php';

class Payment
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO payments (booking_id, client_id, transaction_id, amount, payment_method, status, stripe_session_id) 
                VALUES (:booking_id, :client_id, :transaction_id, :amount, :payment_method, :status, :stripe_session_id)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':booking_id', $data['booking_id']);
        $stmt->bindParam(':client_id', $data['client_id']);
        $stmt->bindParam(':transaction_id', $data['transaction_id']);
        $stmt->bindParam(':amount', $data['amount']);

        $method = $data['payment_method'] ?? 'card';
        $stmt->bindParam(':payment_method', $method);

        $status = $data['status'] ?? 'succeeded';
        $stmt->bindParam(':status', $status);

        $sessionId = $data['stripe_session_id'] ?? null;
        $stmt->bindParam(':stripe_session_id', $sessionId);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getByBookingId($booking_id)
    {
        $sql = "SELECT * FROM payments WHERE booking_id = :booking_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSucceededTotalByBookingId($booking_id)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total
                FROM payments
                WHERE booking_id = :booking_id AND status = 'succeeded'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($row['total'] ?? 0);
    }

    public function existsByStripeSessionId($stripeSessionId)
    {
        $sql = "SELECT COUNT(*) as count FROM payments WHERE stripe_session_id = :stripe_session_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':stripe_session_id', $stripeSessionId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int) ($row['count'] ?? 0)) > 0;
    }

    public function getByClientId($clientId)
    {
        // Fetch all bookings with their payment status and aggregated event details
        // Only show bookings that are either confirmed/completed OR have at least one payment
        $sql = "SELECT b.id as booking_id, b.event_snapshot, b.event_id, b.package_tier as booking_tier,
                       b.total_amount as amount, b.payment_status as booking_payment_status, 
                       b.created_at as created_at, b.status as booking_status, b.event_date as booking_event_date,
                       SUM(p.amount) as paid_amount, MAX(p.created_at) as paid_at,
                       e.title as live_title, e.event_date as live_date, 
                       e.image_path as live_image, e.venue_name as live_venue,
                       t.ticket_code
                FROM bookings b 
                LEFT JOIN payments p ON b.id = p.booking_id AND p.status = 'succeeded'
                LEFT JOIN events e ON b.event_id = e.id
                LEFT JOIN tickets t ON b.id = t.booking_id
                WHERE b.client_id = :client_id 
                  AND b.status != 'cancelled'
                  AND (b.payment_status != 'unpaid' OR b.status IN ('confirmed', 'completed') OR p.id IS NOT NULL)
                GROUP BY b.id
                ORDER BY b.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':client_id', $clientId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map internal status to frontend expected status
        return array_map(function($row) {
            // If booking is fully paid, the total amount should be shown as paid (even if partial was cash)
            if ($row['booking_payment_status'] === 'paid') {
                $row['paid_amount'] = $row['amount'];
                $row['ui_status'] = 'paid';
                $row['ui_label'] = 'Fully Paid';
            } elseif ($row['booking_payment_status'] === 'partially_paid') {
                $row['ui_status'] = 'partial';
                $row['ui_label'] = 'Half Paid';
                // Ensure paid_amount reflects at least the 50% if marked partially paid
                if ((float)($row['paid_amount'] ?? 0) <= 0) {
                    $row['paid_amount'] = (float)$row['amount'] * 0.5;
                }
            } else {
                $row['ui_status'] = 'pending';
                $row['ui_label'] = 'Pending';
                $row['paid_amount'] = $row['paid_amount'] ?? 0;
            }
            
            // For status field used in some JS logic
            $row['status'] = ($row['ui_status'] === 'paid' || $row['ui_status'] === 'partial') ? 'succeeded' : 'pending';
            return $row;
        }, $results);
    }
}
