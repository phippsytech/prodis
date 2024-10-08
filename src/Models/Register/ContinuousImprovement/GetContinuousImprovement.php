<?php
namespace NDISmate\Models\Register\ContinuousImprovement;

use \RedBeanPHP\R as R;

class GetContinuousImprovement
{
    public function __invoke($data)
    {
        $bean = R::load('continuousimprovements', $data['id']);

        return $bean;
    }
}