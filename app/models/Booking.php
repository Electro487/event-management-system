<?php
require_once dirname(__DIR__) . '/config/database.php';

class Booking
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO bookings (event_id, client_id, package_tier, event_date, guest_count, full_name, email, phone, total_amount, service_fee, status) 
                VALUES (:event_id, :client_id, :package_tier, :event_date, :guest_count, :full_name, :email, :phone, :total_amount, :service_fee, :status)";

        $stmt = $this->db->prepare($sql);

        $status = $data['status'] ?? 'pending';

        $stmt->bindParam(':event_id', $data['event_id']);
        $stmt->bindParam(':client_id', $data['client_id']);
        $stmt->bindParam(':package_tier', $data['package_tier']);
        $stmt->bindParam(':event_date', $data['event_date']);
        $stmt->bindParam(':guest_count', $data['guest_count']);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':service_fee', $data['service_fee']);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getByClient($client_id)
    {
        $sql = "SELECT b.*, e.title as event_title, e.image_path as event_image, e.category as event_category, 
                       e.venue_location, e.venue_name, e.packages as event_packages, e.event_date as event_start_date,
                       u.fullname as organizer_name
                FROM bookings b 
                JOIN events e ON b.event_id = e.id 
                JOIN users u ON e.organizer_id = u.id
                WHERE b.client_id = :client_id 
                ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT b.*, e.title as event_title, e.image_path as event_image, e.category as event_category, 
                       e.venue_location, e.venue_name, e.packages as event_packages, e.event_date as event_start_date,
                       u.fullname as organizer_name
                FROM bookings b 
                JOIN events e ON b.event_id = e.id 
                JOIN users u ON e.organizer_id = u.id
                WHERE b.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cancel($id, $client_id)
    {
        $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = :id AND client_id = :client_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':client_id', $client_id);
        return $stmt->execute();
    }
}
