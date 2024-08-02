<?php
namespace NDISmate\Models\Client;

use \RedBeanPHP\R as R;

class GetClient
{
    public function __invoke($data)
    {
        $bean = R::load('clients', $data['id']);
        return $bean;
    }
}
