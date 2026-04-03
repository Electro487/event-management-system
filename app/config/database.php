<?php
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $pdo;
    private $error;

    public function __construct() {
        $envFile = dirname(dirname(dirname(__FILE__))) . '/.env';
        if (file_exists($envFile)) {
            $env = parse_ini_file($envFile);
            $this->host = $env['DB_HOST'] ?? 'localhost';
            $this->user = $env['DB_USER'] ?? 'root';
            $this->pass = $env['DB_PASS'] ?? '';
            $this->dbname = $env['DB_NAME'] ?? 'event_management_system';
        } else {
            $this->host = 'localhost';
            $this->user = 'root';
            $this->pass = '';
            $this->dbname = 'event_management_system';
        }

        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            throw new Exception("Database Connection Failed: " . $this->error);
        }
    }

    /**
     * Get the PDO database connection
     * @return PDO
     */
    public function getConnection() {
        return $this->pdo;
    }
}
