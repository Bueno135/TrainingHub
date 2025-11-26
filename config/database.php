<?php
// ============================================
// config/database.php
// ============================================
require_once __DIR__ . '/env.php';

class Database {
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    private $conn = null;

    public function __construct() {
        $this->host = env('DB_HOST', 'localhost');
        $this->port = env('DB_PORT', 3306);
        $this->dbname = env('DB_DATABASE', 'traininghub');
        $this->username = env('DB_USERNAME', 'root');
        $this->password = env('DB_PASSWORD', '');
    }

    public function connect() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}