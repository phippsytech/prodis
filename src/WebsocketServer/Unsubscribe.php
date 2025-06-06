<?php
namespace NDISmate\WebsocketServer;

// Unsubscribe from a channel
final Class Unsubscribe
{
    public function __invoke($obj, $device_id, $channel)
    {
        foreach ($obj->devices as $device) {
            $data = $obj->devices->offsetGet($device);
            if (isset($data['device_id']) && $data['device_id'] == $device_id) {
                if (isset($data['channels']) && in_array($channel, $data['channels'])) {
                    $data['channels'] = array_diff($data['channels'], [$channel]);
                    $obj->devices->offsetSet($device, $data);
                }
            }
        }
    }
}
