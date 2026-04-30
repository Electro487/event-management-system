<?php
require_once 'app/config/database.php';
$db = new Database();
$pdo = $db->getConnection();
$stmt = $pdo->prepare('DESCRIBE bookings');
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
