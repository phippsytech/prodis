<?php
require __DIR__ . '/../../env.php';
$loader = require (__DIR__ . '/../../vendor/autoload.php');

use NDISmate\Services\MysqlConnectionPoolService\ConnectionPool;

$unix_socket_file = '/tmp/ndismate_mysql.sock';

// # UNIX SOCKET
unlink($unix_socket_file);  // Delete the socket file if it exists

// Sets up the unix socket server for sending data to the websocket server
$unix_socket = new \React\Socket\UnixServer($unix_socket_file);

// Set file permissions using chmod() after the socket is created
$fileMode = 0666;
chmod($unix_socket_file, $fileMode);

// If the server crashes or is stopped, delete the socket file
// src: https://stackoverflow.com/questions/9523240/php-cli-in-windows-handling-ctrl-c-commands
pcntl_signal(SIGINT, function () use ($unix_socket, $unix_socket_file) {
    $unix_socket->close();
    unlink($unix_socket_file);
    exit;
});

$dsn = 'mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATABASE;
$username = MYSQL_USER;
$password = MYSQL_PASSWORD;

$pool = new ConnectionPool($dsn, $username, $password, 10);

$unix_socket->on('connection', function (\React\Socket\ConnectionInterface $connection) use ($pool) {
    $connection->on('data', function ($data) use ($connection, $pool) {
        $message = trim($data);
        echo $message;
        if ($message === 'get_connection') {
            try {
                $pdoConnection = $pool->getConnection();
                $serializedConnection = serialize($pdoConnection);
                $connection->write($serializedConnection . PHP_EOL);
            } catch (\RuntimeException $e) {
                $connection->write('Error: ' . $e->getMessage() . PHP_EOL);
            }
        } elseif ($message === 'release_connection') {
            $pdoConnection = unserialize(trim(substr($data, strlen('release_connection '))));
            if ($pdoConnection instanceof PDO) {
                $pool->releaseConnection($pdoConnection);
                $connection->write('Connection released.' . PHP_EOL);
            } else {
                $connection->write('Invalid connection.' . PHP_EOL);
            }
        } else {
            $connection->write("Unknown command. Use 'get_connection' or 'release_connection'." . PHP_EOL);
        }
    });
});
