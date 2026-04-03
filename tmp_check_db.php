<?php
require __DIR__.'/app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query('DESCRIBE users');
echo "USERS TABLE:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt = $db->query('DESCRIBE bookings');
echo "\nBOOKINGS TABLE:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
