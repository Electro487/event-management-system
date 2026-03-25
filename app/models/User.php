<?php
require_once dirname(dirname(__FILE__)) . '/config/database.php';

class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check password (assume it's hashed using password_hash during registration)
            // Included a fallback to plain text check during dev in case the other dev hasn't hashed yet
            if (password_verify($password, $user['password'])) {
                return $user;
            } else if ($password === $user['password']) {
                return $user;
            }
        }
        return false;
    }
}
