<?php
require_once dirname(__DIR__) . '/config/database.php';

class PromoCode
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO promo_codes (code, discount_percentage, expires_at, created_by, usage_limit) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            strtoupper($data['code']),
            $data['discount_percentage'],
            $data['expires_at'],
            $data['created_by'],
            $data['usage_limit'] ?? null
        ]);
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT p.*, u.fullname as creator_name FROM promo_codes p JOIN users u ON p.created_by = u.id ORDER BY p.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCode($code)
    {
        $stmt = $this->db->prepare("SELECT * FROM promo_codes WHERE code = ? AND expires_at > NOW() AND (usage_limit IS NULL OR usage_count < usage_limit)");
        $stmt->execute([strtoupper($code)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementUsage($id)
    {
        $stmt = $this->db->prepare("UPDATE promo_codes SET usage_count = usage_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM promo_codes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
