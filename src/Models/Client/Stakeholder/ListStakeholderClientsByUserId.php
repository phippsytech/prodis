<?php
namespace NDISmate\Models\Client\Stakeholder;

use \RedBeanPHP\R as R;

class ListStakeholderClientsByUserId
{
    public function __invoke($data, $guard)
    {
        $beans = R::getAll(
            'SELECT                 
                clients.name as name,
                clients.id as id
            FROM stakeholders
            LEFT JOIN clients on (clients.id=stakeholders.client_id)
            WHERE stakeholders.user_id=:user_id',
            [
                // ':user_id' => $data['userId']
                ':user_id' => $guard->user_id
            ]
        );

        return $beans;
    }
}
