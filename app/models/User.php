<?php

class User
{
    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Register user
    public function register($data)
    {
        $pdo = $this->db->getConnection();

        $sql = "INSERT INTO users (fullname, email, password, role, is_verified) VALUES (:fullname, :email, :password, :role, :is_verified)";
        $stmt = $pdo->prepare($sql);

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Use client role by default
        $role = 'client';
        $is_verified = 0; // Default to unverified

        $stmt->bindParam(':fullname', $data['fullname']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':is_verified', $is_verified);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update OTP code for a user
    public function updateOTP($email, $otp, $expires_at)
    {
        $pdo = $this->db->getConnection();
        $sql = "UPDATE users SET otp_code = :otp_code, otp_expires_at = :otp_expires_at WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':otp_code', $otp);
        $stmt->bindParam(':otp_expires_at', $expires_at);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // Verify OTP code
    public function verifyOTP($email, $otp)
    {
        $pdo = $this->db->getConnection();
        // Remove NOW() from SQL and compare in PHP to avoid timezone issues
        $sql = "SELECT otp_expires_at FROM users WHERE email = :email AND otp_code = :otp_code LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':otp_code', $otp);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $expires_at = strtotime($user['otp_expires_at']);
            $current_time = time();

            // Debugging (optional, can be removed later)
            error_log("OTP Verification for $email: Code=$otp, Expires=" . $user['otp_expires_at'] . " (" . $expires_at . "), Current=" . date("Y-m-d H:i:s", $current_time) . " (" . $current_time . ")");

            return $expires_at > $current_time;
        }

        error_log("OTP Verification failed for $email: Code $otp not found in database.");
        return false;
    }

    // Reset password
    public function resetPassword($email, $newPassword)
    {
        $pdo = $this->db->getConnection();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        // Also clear OTP upon successful reset
        $sql = "UPDATE users SET password = :password, otp_code = NULL, otp_expires_at = NULL, is_verified = 1 WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // Mark email as verified
    public function markEmailAsVerified($email)
    {
        $pdo = $this->db->getConnection();
        $sql = "UPDATE users SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // Find user by email to check if email already exists
    public function emailExists($email)
    {
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
    public function login($email, $password)
    {
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

    public function findByEmail($email)
    {
        $pdo = $this->db->getConnection();
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $pdo = $this->db->getConnection();
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countAll()
    {
        $pdo = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function getAll()
    {
        $pdo = $this->db->getConnection();
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByRole($role)
    {
        $pdo = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as count FROM users WHERE role = :role";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function countBlocked()
    {
        $pdo = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as count FROM users WHERE is_blocked = 1";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function updateRole($userId, $role)
    {
        $pdo = $this->db->getConnection();
        $sql = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function toggleBlock($userId, $status)
    {
        $pdo = $this->db->getConnection();
        $sql = "UPDATE users SET is_blocked = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function updateProfilePicture($userId, $path)
    {
        $pdo = $this->db->getConnection();
        $sql = "UPDATE users SET profile_picture = :path WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }
}
