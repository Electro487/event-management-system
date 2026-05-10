<?php
require_once dirname(__DIR__) . '/config/database.php';

class Message
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO messages (sender_id, receiver_id, request_id, message) 
                VALUES (:sender_id, :receiver_id, :request_id, :message)";

        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':sender_id', $data['sender_id']);
        $stmt->bindParam(':receiver_id', $data['receiver_id']);
        $stmt->bindParam(':request_id', $data['request_id']);
        $stmt->bindParam(':message', $data['message']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getByRequestId($request_id)
    {
        $sql = "SELECT m.*, u.fullname as sender_name, u.profile_picture as sender_profile_pic,
                       r.fullname as receiver_name
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                JOIN users r ON m.receiver_id = r.id
                WHERE m.request_id = :request_id
                ORDER BY m.created_at ASC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':request_id', $request_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getConversationsByUserId($user_id)
    {
        // For standard conversations
        $sql = "SELECT m.*, u.fullname as other_person_name, u.profile_picture as other_person_pic 
                FROM messages m
                JOIN users u ON (u.id = m.sender_id OR u.id = m.receiver_id) AND u.id != :user_id
                WHERE m.sender_id = :user_id OR m.receiver_id = :user_id
                GROUP BY u.id
                ORDER BY m.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
