<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$sql = "SELECT e.organizer_id, COUNT(*) as count 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        GROUP BY e.organizer_id";
$stmt = $db->query($sql);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
