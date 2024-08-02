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
}
