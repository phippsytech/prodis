<?php
namespace NDISmate\Models\Register\Training;

use \RedBeanPHP\R as R;

class GetTraining
{
    public function __invoke($data)
    {
        $bean = R::load('trainings', $data['id']);

        return $bean;
    }
}