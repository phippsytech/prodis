<?php
namespace NDISmate\Models\Participant\Service;

use \RedBeanPHP\R as R;

class GetService
{
    public function __invoke($data)
    {
        $bean = R::load('clientplanservices', $data['id']);
        return $bean;
    }
}
