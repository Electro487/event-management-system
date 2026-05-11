<?php
$env = parse_ini_file('.env');
$conn = new mysqli($env['DB_HOST'] ?? 'localhost', $env['DB_USER'] ?? 'root', $env['DB_PASS'] ?? '', $env['DB_NAME'] ?? 'event_management_system');
$res = $conn->query("SELECT DISTINCT status FROM payments");
while($row = $res->fetch_assoc()) {
    echo $row['status'] . "\n";
}
echo "----\n";
$res = $conn->query("SELECT * FROM payments LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
