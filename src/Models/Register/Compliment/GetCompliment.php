<?php
namespace NDISmate\Models\Register\Compliment;

use \RedBeanPHP\R as R;

class GetCompliment
{
    public function __invoke($data)
    {
        $bean = R::load('compliments', $data['id']);

        return $bean;
    }
}