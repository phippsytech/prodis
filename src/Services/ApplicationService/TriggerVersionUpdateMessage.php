<?php
namespace NDISmate\Services\ApplicationService;

use \RedBeanPHP\R as R;

class TriggerVersionUpdateMessage
{
    public function __invoke()
    {
        $json = '{
            "action": "multicast",
            "data": {
                "action": "version_updated",
                "data": {}
            }
        }';

        $socket = stream_socket_client('unix:///tmp/ndismate.sock');

        if ($socket !== false) {
            fwrite($socket, $json);
            $response = fread($socket, 8192);
            return $response;
        } else {
            return 'ERROR: Unable to open socket';
        }
    }
}
