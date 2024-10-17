<?php
namespace NDISmate\Models\Register\Risk;

use \RedBeanPHP\R as R;

class GetRisk
{
    public function __invoke($data)
    {
        $bean = R::load('risks', $data['id']);

        return $bean;
    }
}