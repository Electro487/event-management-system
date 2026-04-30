<?php
require_once 'app/config/database.php';
require_once 'app/models/User.php';
require_once 'api/src/services/UserService.php';

try {
    $service = new UserService();
    $res = $service->getAll();
    print_r($res);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
}
