<?php

namespace NDISmate\Services\MysqlConnectionPoolService;

class GetConnection
{
    function __invoke()
    {
        $socket = stream_socket_client('unix:///tmp/ndismate_mysql.sock', $errno, $errorMessage);

        if ($socket !== false) {
            // Request a connection
            fwrite($socket, "get_connection\n");
            $serializedConnection = stream_get_contents($socket);
            $pdoConnection = unserialize($serializedConnection);

            if ($pdoConnection instanceof PDO) {
                return $pdoConnection;
            } else {
                throw new UnexpectedValueException("Failed to get a valid PDO Connection.\n");
            }

            fclose($socket);
        } else {
            throw new UnexpectedValueException("Failed to connect: $errorMessage");
        }
    }
}
