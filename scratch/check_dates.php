<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$sql = "SELECT b.event_date FROM bookings b JOIN events e ON b.event_id = e.id WHERE e.organizer_id = 1";
$stmt = $db->query($sql);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
