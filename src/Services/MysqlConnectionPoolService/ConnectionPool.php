<?php

namespace NDISmate\Services\MysqlConnectionPoolService;

use PDO;
use PDOException;

class ConnectionPool
{
    private array $pool = [];
    private string $dsn;
    private string $username;
    private string $password;
    private int $poolSize;

    public function __construct(string $dsn, string $username, string $password, int $poolSize = 5)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->poolSize = $poolSize;

        for ($i = 0; $i < $poolSize; $i++) {
            $this->pool[] = $this->createConnection();
        }
    }

    private function createConnection(): PDO
    {
        try {
            $connection = new PDO($this->dsn, $this->username, $this->password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $e) {
            throw new \RuntimeException('Unable to create a database connection: ' . $e->getMessage());
        }
    }

    private function isConnectionAlive(PDO $connection): bool
    {
        try {
            $stmt = $connection->query('SELECT 1');
            return $stmt !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getConnection(): PDO
    {
        if (empty($this->pool)) {
            throw new \RuntimeException('No available connections in the pool.');
        }

        $connection = array_pop($this->pool);

        if (!$this->isConnectionAlive($connection)) {
            $connection = $this->createConnection();
        }

        return $connection;
    }

    public function releaseConnection(PDO $connection): void
    {
        if (count($this->pool) < $this->poolSize) {
            $this->pool[] = $connection;
        }
    }
}
