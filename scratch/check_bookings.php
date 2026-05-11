<?php
require_once 'app/config/database.php';
session_start();
$organizer_id = 4; // Assuming this is the current organizer based on previous context or common ID
$db = (new Database())->getConnection();
$sql = "SELECT b.event_date, b.total_amount, b.status, b.payment_status 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE e.organizer_id = :org_id";
$stmt = $db->prepare($sql);
$stmt->execute([':org_id' => $organizer_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($bookings);
