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
}
