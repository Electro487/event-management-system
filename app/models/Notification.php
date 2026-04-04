<?php

class Notification
{
    private $db;

    public function __construct()
    {
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $dbname = 'event_management_system';

        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function create($userId, $title, $message, $type = 'info', $relatedId = null)
    {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, title, message, type, related_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $title, $message, $type, $relatedId]);
    }

    public function getLatestByUser($userId, $limit = 5)
    {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function markAsRead($userId, $id = null)
    {
        if ($id) {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND id = ?");
            return $stmt->execute([$userId, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            return $stmt->execute([$userId]);
        }
    }

    public function markAsUnread($userId, $id = null)
    {
        if (is_array($id)) {
            $placeholders = implode(',', array_fill(0, count($id), '?'));
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 0 WHERE user_id = ? AND id IN ($placeholders)");
            return $stmt->execute(array_merge([$userId], $id));
        } elseif ($id) {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 0 WHERE user_id = ? AND id = ?");
            return $stmt->execute([$userId, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 0 WHERE user_id = ?");
            return $stmt->execute([$userId]);
        }
    }
    
    public function delete($userId, $id) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function getAllByUser($userId, $type = null)
    {
        if ($type === 'event_updates') {
            $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? AND type IN ('event_update', 'event_delete') ORDER BY created_at DESC");
            $stmt->execute([$userId]);
        } elseif ($type) {
            $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? AND type = ? ORDER BY created_at DESC");
            $stmt->execute([$userId, $type]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteAll($userId)
    {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
