<?php

namespace NDISmate\Services\MysqlConnectionPoolService;

class ReleaseConnection
{
    function __invoke($pdoConnection)
    {
        $socket = stream_socket_client('unix:///tmp/ndismate_mysql.sock', $errno, $errorMessage);

        if ($socket !== false) {
            if ($pdoConnection instanceof PDO) {
                // Release the connection back to the pool
                $serializedConnection = serialize($pdoConnection);
                fwrite($client, "release_connection $serializedConnection\n");
                // $response = stream_get_contents($client);
                // echo $response;
            } else {
                // echo "Failed to get a valid connection.\n";
            }
            fclose($client);
        } else {
            throw new UnexpectedValueException("Failed to connect: $errorMessage");
        }
    }
}
