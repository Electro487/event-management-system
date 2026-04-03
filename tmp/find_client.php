<?php
require_once dirname(__DIR__) . '/app/config/database.php';
try {
/** @var PDO $db */
$db = (new Database())->getConnection();
    $sql = "SELECT email, fullname, role FROM users WHERE role = 'client' LIMIT 5";
    $stmt = $db->query($sql);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clients, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
