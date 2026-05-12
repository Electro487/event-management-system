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

    public function getAll($rating = null, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
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

        $query .= " ORDER BY f.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($query);
        
        // Bind parameters manually for LIMIT/OFFSET
        $idx = 1;
        if ($rating) {
            $stmt->bindValue($idx++, $rating);
        }
        $stmt->bindValue($idx++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($idx++, (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($feedbacks as &$fb) {
            $fb['replies'] = $this->getReplies($fb['id']);
        }
        return $feedbacks;
    }

    public function getTotalCount($rating = null)
    {
        $query = "SELECT COUNT(*) FROM feedbacks";
        $params = [];
        if ($rating) {
            $query .= " WHERE rating = ?";
            $params[] = $rating;
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getRatingStats()
    {
        $stmt = $this->db->prepare("SELECT rating, COUNT(*) as count FROM feedbacks GROUP BY rating");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $total = 0;
        $sum = 0;
        
        foreach ($rows as $row) {
            $r = (int)$row['rating'];
            $c = (int)$row['count'];
            $counts[$r] = $c;
            $total += $c;
            $sum += ($r * $c);
        }
        
        $avg = $total > 0 ? round($sum / $total, 1) : 0;
        
        return [
            'total' => $total,
            'avg' => $avg,
            'counts' => $counts
        ];
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

    public function getByClient($clientId, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("SELECT * FROM feedbacks WHERE client_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $clientId);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($feedbacks as &$fb) {
            $fb['replies'] = $this->getReplies($fb['id']);
        }
        return $feedbacks;
    }

    public function getTotalCountByClient($clientId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM feedbacks WHERE client_id = ?");
        $stmt->execute([$clientId]);
        return (int)$stmt->fetchColumn();
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
