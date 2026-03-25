<?php

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Register user
    public function register($data) {
        $pdo = $this->db->getConnection();
        
        $sql = "INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :password, :role)";
        $stmt = $pdo->prepare($sql);
        
        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Use client role by default
        $role = 'client';
        
        $stmt->bindParam(':fullname', $data['fullname']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Find user by email to check if email already exists
    public function emailExists($email) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return true;
        }
        
        return false;
    }

    // Login user
    public function login($email, $password) {
        $pdo = $this->db->getConnection();

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check password (assume it's hashed using password_hash during registration)
            // Included a fallback to plain text check during dev in case legacy data is present
            if (password_verify($password, $user['password'])) {
                return $user;
            } else if ($password === $user['password']) {
                return $user;
            }
        }
        
        return false;
    }
}
