<?php

namespace NDISmate\WebsocketServer;

use \Ratchet\ConnectionInterface;
use \Ratchet\MessageComponentInterface;

class Server implements MessageComponentInterface
{
    /*
     * $json always contains message in json format
     * $message always contains message in the format of an array
     */

    public $devices;

    public function __construct()
    {
        echo "constructing websocket";
        $this->devices = new \SplObjectStorage;
    }

    // # Ratchet functions
    public function onOpen(ConnectionInterface $conn)
    {
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

    // # Additional functions to aid with sending and receiving messages
    public function handleSocketData($json)
    {
        $message = json_decode($json, true);
        (new \NDISmate\WebsocketServer\HandleSocketData)($this, $message);
    }

    public function multicast($message)
    {
        (new \NDISmate\WebsocketServer\Multicast)($this, $message);
    }
}
