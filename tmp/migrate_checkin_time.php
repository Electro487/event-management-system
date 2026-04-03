<?php
require_once dirname(__DIR__) . '/app/config/database.php';
try {
    $db = (new Database())->getConnection();
    $sql = "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS checkin_time VARCHAR(20) DEFAULT '10:00 AM' AFTER phone";
    $db->exec($sql);
    echo "Successfully added 'checkin_time' column (or it already exists).\n";
} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
