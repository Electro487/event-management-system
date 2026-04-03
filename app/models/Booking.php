<?php
require_once dirname(__DIR__) . '/config/database.php';

class Booking
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO bookings (event_id, client_id, package_tier, event_date, guest_count, full_name, email, phone, checkin_time, total_amount, status, payment_status) 
                VALUES (:event_id, :client_id, :package_tier, :event_date, :guest_count, :full_name, :email, :phone, :checkin_time, :total_amount, :status, :payment_status)";

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
        $stmt->bindParam(':checkin_time', $data['checkin_time']);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':status', $status);
        $paymentStatus = $data['payment_status'] ?? 'unpaid';
        $stmt->bindParam(':payment_status', $paymentStatus);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getByClient($client_id)
    {
        $sql = "SELECT b.*, e.title as event_title, e.image_path as event_image, e.category as event_category, 
                       e.venue_location, e.venue_name, e.packages as event_packages, e.event_date as event_start_date,
                       e.organizer_id, u.fullname as organizer_name
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
                       e.description as event_description, e.venue_location, e.venue_name, e.packages as event_packages, 
                       e.event_date as event_start_date, e.organizer_id, u.fullname as organizer_name,
                       c.profile_picture as client_profile_pic
                FROM bookings b 
                JOIN events e ON b.event_id = e.id 
                JOIN users u ON e.organizer_id = u.id
                LEFT JOIN users c ON b.client_id = c.id
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

    public function getByOrganizer($organizer_id)
    {
        $sql = "SELECT b.*, e.title as event_title, e.image_path as event_image, e.category as event_category, 
                       e.venue_location, e.venue_name, e.packages as event_packages, e.event_date as event_start_date,
                       e.organizer_id, c.fullname as client_user_name, c.profile_picture as client_profile_pic
                FROM bookings b 
                JOIN events e ON b.event_id = e.id 
                LEFT JOIN users c ON b.client_id = c.id
                WHERE e.organizer_id = :organizer_id 
                ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $sql = "SELECT b.*, e.title as event_title, e.image_path as event_image, e.category as event_category, 
                       e.venue_location, e.venue_name, e.packages as event_packages, e.event_date as event_start_date,
                       e.organizer_id, c.fullname as client_user_name, c.profile_picture as client_profile_pic
                FROM bookings b 
                JOIN events e ON b.event_id = e.id 
                LEFT JOIN users c ON b.client_id = c.id
                ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE bookings SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updatePaymentStatus($id, $paymentStatus)
    {
        $sql = "UPDATE bookings SET payment_status = :payment_status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':payment_status', $paymentStatus);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function countAll()
    {
        $sql = "SELECT COUNT(*) as count FROM bookings";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function countByStatus($status)
    {
        $sql = "SELECT COUNT(*) as count FROM bookings WHERE status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function getRecent($limit = 5)
    {
        $sql = "SELECT b.*, e.title as event_title, c.fullname as client_name, c.profile_picture as client_profile_pic
                FROM bookings b
                JOIN events e ON b.event_id = e.id
                JOIN users c ON b.client_id = c.id
                ORDER BY b.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByOrganizer($organizer_id)
    {
        $sql = "SELECT COUNT(*) as count FROM bookings b JOIN events e ON b.event_id = e.id WHERE e.organizer_id = :organizer_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function countByStatusForOrganizer($organizer_id, $status)
    {
        $sql = "SELECT COUNT(*) as count FROM bookings b JOIN events e ON b.event_id = e.id WHERE e.organizer_id = :organizer_id AND b.status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function getRecentByOrganizer($organizer_id, $limit = 5)
    {
        $sql = "SELECT b.*, e.title as event_name, b.package_tier as package_name, c.fullname as client_name, c.profile_picture as client_profile_pic
                FROM bookings b
                JOIN events e ON b.event_id = e.id
                JOIN users c ON b.client_id = c.id
                WHERE e.organizer_id = :organizer_id
                ORDER BY b.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRevenueByOrganizer($organizer_id)
    {
        $sql = "SELECT SUM(b.total_amount) as total FROM bookings b JOIN events e ON b.event_id = e.id WHERE e.organizer_id = :organizer_id AND b.status = 'confirmed'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }

    public function exists($event_id, $client_id)
    {
        $sql = "SELECT COUNT(*) as count FROM bookings WHERE event_id = :event_id AND client_id = :client_id AND status != 'cancelled'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }
}
