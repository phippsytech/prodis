<?php
namespace NDISmate\Models\Register\ConflictOfInterest;


use \RedBeanPHP\R as R;

class GetConflictOfInterest
{
    public function __invoke($data)
    {
        $bean = R::load('conflictofinterests', $data['id']);

        return $bean;
    }
}