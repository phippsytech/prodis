<?php
namespace NDISmate\Models\Client;

use \RedBeanPHP\R as R;

class ListClients
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            'SELECT clients.id as client_id, name as client_name , archived
            FROM clients 
            '
        );
        return $beans;
    }
}
