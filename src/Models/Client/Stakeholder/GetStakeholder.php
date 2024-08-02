<?php
namespace NDISmate\Models\Client\Stakeholder;

use \RedBeanPHP\R as R;

class GetStakeholder
{
    public function __invoke($data)
    {
        $bean = R::load('stakeholders', $data['id']);
        return $bean;
    }
}
