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

        if(!$from->token) {
            if(isset($message['token']))
                $token = $message['token'];
                
                try {
                    // Decode and verify the JWT
                    $decoded = JWT::decode($token, new Key(PUBLIC_KEY, 'RS256'));

                    $hashids = new Hashids(HASH_SALT, 8);
                    $user_id = $hashids->decode($decoded->user_hash)[0];

                    // Assuming the user_id is stored in the 'sub' claim of the JWT
                    $from->userId = $user_id;
                    $from->token = $token;

                    // Notify the client of successful authentication
                    $from->send(json_encode(['status' => 'authenticated']));

                } catch (Exception $e) {
                    // Send error and close the connection if the token is invalid
                    $from->send(json_encode(['status' => 'error', 'message' => 'Invalid token']));
                    $from->close();
                }
            } else {
                // Send error if no token is provided and close the connection
                $from->send(json_encode(['status' => 'error', 'message' => 'No token provided']));
                $from->close();
            }

        } else {
            // If already authenticated, handle the message as usual
            (new \NDISmate\WebsocketServer\HandleSocketData)($this, $message);
        }
        
        
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
