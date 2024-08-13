<?php
namespace NDISmate\Models\Client;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use \RedBeanPHP\R as R;

class ListClients
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            'SELECT clients.id as client_id, name as client_name , archived, on_hold
            FROM clients 
            '
        );
        $beans = (new ConvertFieldsToBoolean)($beans, ['on_hold']);
        return $beans;
    }
}
