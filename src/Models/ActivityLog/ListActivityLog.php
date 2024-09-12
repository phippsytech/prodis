<?php
namespace NDISmate\Models\ActivityLog;

use \RedBeanPHP\R as R;

class ListActivityLog
{
    public function __invoke($data)
    {
        $bean = R::findAll('activitylogs');

        return $bean;
    }
}