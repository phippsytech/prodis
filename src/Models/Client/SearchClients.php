<?php
namespace NDISmate\Models\Client;

use \RedBeanPHP\R as R;

class SearchClients
{
    public function __invoke($data)
    {
        $search = $data['search'];
        $beans = R::getAll(
            'SELECT id, name 
            FROM clients 
            WHERE name LIKE :search 
            AND (archived != 1 or archived is null)',
            [':search' => "%$search%"]
        );
        return $beans;
    }
}
