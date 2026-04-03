<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$sql = "SELECT e.id, e.title, e.status, 
        (SELECT COUNT(*) FROM bookings b WHERE b.event_id = e.id AND b.status != 'cancelled') as count 
        FROM events e LIMIT 10";
$result = $db->query($sql);
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | Title: " . $row['title'] . " | Status: " . $row['status'] . " | Bookings: " . $row['count'] . "\n";
}
?>
