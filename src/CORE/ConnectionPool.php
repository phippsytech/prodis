<?php
namespace NDISmate\CORE;

use PDO;
use PDOException;

class ConnectionPool
{
    private static $instance = null;
    private $pools = [];
    private $maxConnections = 10;
    private $timeout = 30;  // Connection timeout in seconds
    private $idleTimeout = 300;  // Idle timeout in seconds

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection($tenant)
    {
        if (!isset($this->pools[$tenant])) {
            $this->pools[$tenant] = [];
        }

        while (count($this->pools[$tenant]) > 0) {
            $connectionData = array_pop($this->pools[$tenant]);
            if ($this->isValidConnection($connectionData['connection'])) {
                $this->pools[$tenant][] = $connectionData;
                return $connectionData['connection'];
            }
        }

        return $this->createConnection($tenant);
    }

    public function releaseConnection($tenant, $connection)
    {
        if (!isset($this->pools[$tenant])) {
            $this->pools[$tenant] = [];
        }

        if (count($this->pools[$tenant]) < $this->maxConnections) {
            $this->pools[$tenant][] = ['connection' => $connection, 'lastUsed' => time()];
        }
    }

    private function createConnection($tenant)
    {
        if (!isset(DB_CONFIGS[$tenant])) {
            die('Database configuration not found: ' . $tenant);
        }

        $config = DB_CONFIGS[$tenant];
        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['name'];

        try {
            $pdo = new PDO($dsn, $config['user'], $config['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_TIMEOUT, $this->timeout);  // Set timeout
            return $pdo;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    private function isValidConnection($connection)
    {
        try {
            $connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function removeIdleConnections()
    {
        $currentTime = time();
        foreach ($this->pools as $tenant => $connections) {
            foreach ($connections as $index => $connectionData) {
                if ($currentTime - $connectionData['lastUsed'] > $this->idleTimeout) {
                    unset($this->pools[$tenant][$index]);
                }
            }
        }
    }
}
