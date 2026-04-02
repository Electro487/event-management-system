<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$db->exec("ALTER TABLE bookings ADD COLUMN payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid' AFTER status");
echo "Column payment_status added successfully.";
?>
