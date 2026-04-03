<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$result = $db->query("DESCRIBE events");
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
?>
