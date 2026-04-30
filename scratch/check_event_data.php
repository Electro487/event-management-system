<?php
require_once 'app/config/database.php';
$db = new Database();
$pdo = $db->getConnection();
$stmt = $pdo->prepare('SELECT id, title, description, packages FROM events WHERE id = 6');
$stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));
