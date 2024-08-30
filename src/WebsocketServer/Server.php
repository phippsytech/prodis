<?php

namespace NDISmate\WebsocketServer;

use \Ratchet\ConnectionInterface;
use \Ratchet\MessageComponentInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Hashids\Hashids;

class Server implements MessageComponentInterface
{
    /*
     * $json always contains message in json format
     * $message always contains message in the format of an array
     */

    public $devices;

    public function __construct()
    {
        $this->devices = new \SplObjectStorage;
    }

    // # Ratchet functions
    public function onOpen(ConnectionInterface $conn)
    {
        $conn->send(json_encode(['action' => 'requestToken']));
        (new \NDISmate\WebsocketServer\Connect)($this, $conn);
        // echo "New connection ". $conn->resourceId;
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove the closed connection.
        $this->devices->offsetUnset($conn);
        $this->devices->detach($conn);
        // echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    
    public function onMessage(ConnectionInterface $from, $json)
    {
        $message = json_decode($json, true);
        (new \NDISmate\WebsocketServer\HandleSocketData)($this, $message);

         
    }

}
