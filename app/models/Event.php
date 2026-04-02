<?php
require_once dirname(__DIR__) . '/config/database.php';

class Event {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO events (organizer_id, title, description, category, status, image_path, event_date, venue_name, venue_location, packages) 
                VALUES (:organizer_id, :title, :description, :category, :status, :image_path, :event_date, :venue_name, :venue_location, :packages)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':organizer_id', $data['organizer_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':image_path', $data['image_path']);
        $stmt->bindParam(':event_date', $data['event_date']);
        $stmt->bindParam(':venue_name', $data['venue_name']);
        $stmt->bindParam(':venue_location', $data['venue_location']);
        $stmt->bindParam(':packages', $data['packages']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * @return array
     */
    public function getAllByOrganizer($organizer_id): array {
        $sql = "SELECT * FROM events WHERE organizer_id = :organizer_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalEvents($organizer_id) {
        $sql = "SELECT COUNT(*) as total FROM events WHERE organizer_id = :organizer_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    public function getUpcomingEvents($organizer_id, $limit = 5) {
        $sql = "SELECT id, title, category, image_path, event_date 
                FROM events 
                WHERE organizer_id = :organizer_id AND event_date >= CURDATE()
                ORDER BY event_date ASC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT * FROM events WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE events SET 
                title = :title, 
                description = :description, 
                category = :category, 
                status = :status, 
                image_path = :image_path, 
                event_date = :event_date, 
                venue_name = :venue_name, 
                venue_location = :venue_location, 
                packages = :packages 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':image_path', $data['image_path']);
        $stmt->bindParam(':event_date', $data['event_date']);
        $stmt->bindParam(':venue_name', $data['venue_name']);
        $stmt->bindParam(':venue_location', $data['venue_location']);
        $stmt->bindParam(':packages', $data['packages']);

        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM events WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
