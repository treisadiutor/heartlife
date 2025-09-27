<?php

class Model {
    protected $db;

    public function __construct() {
        if (!getenv('DB_HOST')) {
            if (file_exists(__DIR__ . '/../../.env')) {
                $lines = file(__DIR__ . '/../../.env');
                foreach ($lines as $line) {
                    if (preg_match('/^([A-Z_]+)=(.*)$/', trim($line), $matches)) {
                        putenv("{$matches[1]}={$matches[2]}");
                    }
                }
            }
        }

        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $port = getenv('DB_PORT') ?: '3306';

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
            $this->db = new PDO($dsn, $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}