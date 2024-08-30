<?php
namespace NDISmate\WebsocketServer;

// Handle messages that come in via the unix socket
final Class HandleSocketData
{


    

    public function __invoke($obj, $message)
    {
        // if (!isset($message['action']) || !isset($message['data']) ) return;
        if (!isset($message['action']))
            return;

        switch ($message['action']) {
            case 'authenticate':
                if (!isset($message['token']))
                    return;
                (new \NDISmate\WebsocketServer\Authenticate)($obj, $message['token']);
                break;

            case 'sendToChannel':
                if (!isset($message['channel']))
                    return;
                (new \NDISmate\WebsocketServer\SendToChannel)($obj, $message['channel'], $message['data']);
                break;

            case 'sendToDevice':
                if (!isset($message['device']))
                    return;
                (new \NDISmate\WebsocketServer\SendToDevice)($obj, $message['device'], $message['data']);
                break;

            case 'multicast':
                (new \NDISmate\WebsocketServer\Multicast)($obj, $message['data']);
                break;

            case 'subscribe':
                if (!isset($message['device']) || !isset($message['channel']))
                    return;
                (new \NDISmate\WebsocketServer\Subscribe)($obj, $message['device'], $message['channel']);
                break;

            case 'unsubscribe':
                if (!isset($message['device']) || !isset($message['channel']))
                    return;
                (new \NDISmate\WebsocketServer\Unsubscribe)($obj, $message['device'], $message['channel']);
                break;
        }
    }


    private function guard($obj){
        if (!$this->devices->offsetExists($from) || !isset($this->devices->offsetGet($from)['token'])) {
  
            if (isset($message['token'])) {
                (new \NDISmate\WebsocketServer\Authenticate)($this, $message['token']);
            } else {
                $from->send(json_encode(['action' => 'tokenRejected', 'message' => 'Token not provided']));
                $from->close();
            }
        } else {
            $token = $this->devices->offsetGet($from)['token'];
            if ($this->validateToken($token)) {
                // If the token is still valid, handle the message
                (new \NDISmate\WebsocketServer\HandleSocketData)($this, $message);
            } else {
                // Token is no longer valid, prompt for re-authentication
                $from->send(json_encode(['action' => 'requestToken']));
            }
        }
    }

    private function validateToken($token)
    {
        try {
            // Decode and verify the JWT
            JWT::decode($token, new Key(PUBLIC_KEY, 'RS256'));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}
