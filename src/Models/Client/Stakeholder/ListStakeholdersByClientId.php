<?php
namespace NDISmate\Models\Client\Stakeholder;

use \RedBeanPHP\R as R;

class ListStakeholdersByClientId
{
    public function __invoke($data)
    {
        $beans = R::getAll('SELECT * from stakeholders  WHERE client_id = :client_id ', [':client_id' => $data['client_id']]);
        return $beans;
    }
}
