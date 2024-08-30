<?php
namespace NDISmate\WebsocketServer;

use \Ratchet\ConnectionInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Hashids\Hashids;

// Authenticate
final Class Authenticate
{
    public function __invoke(ConnectionInterface $conn, $token)
    {
        try {
            // Decode and verify the JWT
            $decoded = JWT::decode($token, new Key(PUBLIC_KEY, 'RS256'));

            $hashids = new Hashids(HASH_SALT, 8);
            $user_id = $hashids->decode($decoded->user_hash)[0];

            // Update the user ID and token in the connection using offsetSet
            $data = $this->devices->offsetGet($conn);
            $data['userId'] = $user_id;
            $data['token'] = $token;
            
            $this->devices->offsetSet($conn, $data);

            // Notify the client of successful authentication
            $conn->send(json_encode(['action' => 'authenticated']));
        } catch (\Exception $e) {
            // Send error and close the connection if the token is invalid
            $conn->send(json_encode(['action' => 'tokenRejected', 'message' => 'Invalid Token']));
            $conn->close();
        } 
    }

}
