<?php

class Feedback
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO feedbacks (client_id, rating, comment) VALUES (?, ?, ?)");
        if ($stmt->execute([$data['client_id'], $data['rating'], $data['comment']])) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getAll($rating = null)
    {
        $query = "
            SELECT f.*, u.fullname as client_name, u.profile_picture 
            FROM feedbacks f 
            JOIN users u ON f.client_id = u.id 
        ";
        
        $params = [];
        if ($rating) {
            $query .= " WHERE f.rating = ? ";
            $params[] = $rating;
        }
        
        $query .= " ORDER BY f.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Attach replies to each feedback
        foreach ($feedbacks as &$fb) {
            $fb['replies'] = $this->getReplies($fb['id']);
        }
        return $feedbacks;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM feedbacks WHERE id = ?");
        $stmt->execute([$id]);
        $fb = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fb) {
            $fb['replies'] = $this->getReplies($id);
        }
        return $fb;
    }

    public function getReplies($feedbackId)
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.fullname as user_name, u.role as user_role, u.profile_picture
            FROM feedback_replies r
            JOIN users u ON r.user_id = u.id
            WHERE r.feedback_id = ?
            ORDER BY r.created_at ASC
        ");
        $stmt->execute([$feedbackId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addReply($feedbackId, $userId, $replyText, $parentReplyId = null)
    {
        $stmt = $this->db->prepare("INSERT INTO feedback_replies (feedback_id, user_id, reply_text, parent_reply_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$feedbackId, $userId, $replyText, $parentReplyId]);
    }

    public function getByClient($clientId)
    {
        $stmt = $this->db->prepare("SELECT * FROM feedbacks WHERE client_id = ? ORDER BY created_at DESC");
        $stmt->execute([$clientId]);
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($feedbacks as &$fb) {
            $fb['replies'] = $this->getReplies($fb['id']);
        }
        return $feedbacks;
    }

    public function updateFeedback($id, $comment, $clientId)
    {
        $stmt = $this->db->prepare("UPDATE feedbacks SET comment = ? WHERE id = ? AND client_id = ?");
        return $stmt->execute([$comment, $id, $clientId]);
    }

    public function updateReply($id, $replyText, $userId)
    {
        $stmt = $this->db->prepare("UPDATE feedback_replies SET reply_text = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$replyText, $id, $userId]);
    }
}
