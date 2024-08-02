<?php
namespace NDISmate\Models\Client\Stakeholder;

use \RedBeanPHP\R as R;

class ListStakeholders
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            'SELECT clients.id as client_id, name , archived
            FROM clients 
            '
        );
        return $beans;
    }
}
