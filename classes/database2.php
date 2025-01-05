<?php

class Database {
    private $host = DB_HOST; // Use the constant from config.php
    private $username = DB_USER; // Use the constant from config.php
    private $password = DB_PASS; // Use the constant from config.php
    private $dbname = DB_NAME;   // Use the constant from config.php

    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function read($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function write($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        return $stmt->execute($params);
    }
}


?>