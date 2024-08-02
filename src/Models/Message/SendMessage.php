<?php
namespace NDISmate\Models\Message;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class SendMessage{

    function __invoke($data){

        $json = '{
            "action": "multicast",
            "device": "1234567",
            "data": {
                "action": "notification",
                "data": {
                    "title": "New version available",
                    "message": "A new version of this app is available. When you are ready, just reload the page to get it."
                }
            }
        }';
        

        $socket = stream_socket_client('unix:///tmp/ndismate.sock');


        if ($socket !== false) {
            
                fwrite($socket, $json);
                $response = fread($socket, 8192);
                return $response;
            
        } else {
            return "ERROR: Unable to open socket";
            
        }

    }

}