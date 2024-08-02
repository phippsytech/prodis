<?php
namespace NDISmate;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Websocket implements MessageComponentInterface
{
    public $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo "starting is now\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "New connection from {$conn->remoteAddress}! ({$conn->resourceId})\n";
        $this->clients->attach(
            $conn
        );

        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        $json = [
            'action' => 'device'
        ];

        $this->clients->offsetSet($conn, $json);

        $conn->send(json_encode($json));
    }

    public function multicast($from, $payload)
    {
        foreach ($this->clients as $client) {
            $payload['resourceID'] = $from->resourceId;
            if ($client->resourceId != $from->resourceId) {
                $client->send(json_encode($payload));
            }
        }
    }

    public function send_to_client($client_id, $payload)
    {
        foreach ($this->clients as $client) {
            $data = $this->clients->offsetGet($client);
            if ($data['client_id'] == $client_id) {
                $msg = json_encode($payload);
                $client->send($msg);
            }
        }
    }

    public function sendPong($from)
    {
        foreach ($this->clients as $client) {
            if ($client->resourceId == $from->resourceId) {
                $client->send('1');
            }
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if ($msg == '1') {
            $this->sendPong($from);
            return;
        }
        $data = json_decode($msg, true);
        $this->multicast($from, $data);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        unset($this->client_ids[$this->clients[$conn]]);
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
