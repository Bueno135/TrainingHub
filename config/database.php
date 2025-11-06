<?php
// ============================================
// config/database.php
// ============================================
class Database {
    private $host = 'localhost';
    private $dbname = 'traininghub';
    private $username = 'root';
    private $password = ''; // Ajustar conforme seu ambiente
    private $conn = null;

    public function connect() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
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