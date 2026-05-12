<?php
require_once dirname(__DIR__) . '/config/database.php';

class CustomEventRequest
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO custom_event_requests (group_event_id, client_id, organizer_id, base_package_tier, event_date, guest_count, custom_packages, proposed_price, status) 
                VALUES (:group_event_id, :client_id, :organizer_id, :base_package_tier, :event_date, :guest_count, :custom_packages, :proposed_price, :status)";

        $stmt = $this->db->prepare($sql);
        
        $status = $data['status'] ?? 'pending';

        $stmt->bindParam(':group_event_id', $data['group_event_id']);
        $stmt->bindParam(':client_id', $data['client_id']);
        $stmt->bindParam(':organizer_id', $data['organizer_id']);
        $stmt->bindParam(':base_package_tier', $data['base_package_tier']);
        $stmt->bindParam(':event_date', $data['event_date']);
        $stmt->bindParam(':guest_count', $data['guest_count']);
        $stmt->bindParam(':custom_packages', $data['custom_packages']);
        $stmt->bindParam(':proposed_price', $data['proposed_price']);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getById($id)
    {
        $sql = "SELECT c.*, e.title as event_title, e.image_path as event_image, e.category as event_category, 
                       u1.fullname as client_name, u2.fullname as organizer_name
                FROM custom_event_requests c
                JOIN events e ON c.group_event_id = e.id
                JOIN users u1 ON c.client_id = u1.id
                JOIN users u2 ON c.organizer_id = u2.id
                WHERE c.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByClientId($client_id)
    {
        $sql = "SELECT c.*, e.title as event_title, e.image_path as event_image, e.category as event_category, u2.fullname as organizer_name 
                FROM custom_event_requests c
                JOIN events e ON c.group_event_id = e.id
                JOIN users u2 ON c.organizer_id = u2.id
                WHERE c.client_id = :client_id
                ORDER BY c.updated_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByOrganizerId($organizer_id)
    {
        $sql = "SELECT c.*, e.title as event_title, e.image_path as event_image, u1.fullname as client_name 
                FROM custom_event_requests c
                JOIN events e ON c.group_event_id = e.id
                JOIN users u1 ON c.client_id = u1.id
                WHERE c.organizer_id = :organizer_id
                ORDER BY c.updated_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE custom_event_requests SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updatePriceAndPackage($id, $proposedPrice, $customPackages)
    {
        $sql = "UPDATE custom_event_requests 
                SET proposed_price = :proposed_price, custom_packages = :custom_packages 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':proposed_price', $proposedPrice);
        $stmt->bindParam(':custom_packages', $customPackages);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
