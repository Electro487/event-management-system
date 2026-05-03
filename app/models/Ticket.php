<?php
require_once dirname(__DIR__) . '/config/database.php';

class Ticket {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO tickets (booking_id, ticket_code, status) 
                VALUES (:booking_id, :ticket_code, :status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':booking_id', $data['booking_id']);
        $stmt->bindParam(':ticket_code', $data['ticket_code']);
        $stmt->bindParam(':status', $data['status']);

        return $stmt->execute();
    }

    public function getByBooking($booking_id) {
        $sql = "SELECT * FROM tickets WHERE booking_id = :booking_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
