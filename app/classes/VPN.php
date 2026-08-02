<?php

use VPN\{Wg};

class VPN
{
    use Wg;

    private \PDO $db;
    private $fm;

    public function __construct()
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

        try {
            $this->db = new \PDO($dsn, DB_USER, DB_PASS, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
            $this->db->exec("SET SESSION innodb_lock_wait_timeout = 5");
        } catch (\PDOException $e) {
            // Connection failure is unrecoverable for this class — surface it clearly
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }

        $this->fm = new \Format(); // note: app\helpers\Format, not VPN\Format — see note below
    }
}