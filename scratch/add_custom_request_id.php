<?php
require_once dirname(__DIR__) . '/app/config/database.php';

try {
    $db = (new Database())->getConnection();
    $sql = "ALTER TABLE bookings ADD COLUMN custom_request_id INT(11) DEFAULT NULL AFTER event_id, ADD INDEX (custom_request_id)";
    $db->exec($sql);
    echo "Column custom_request_id added successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
