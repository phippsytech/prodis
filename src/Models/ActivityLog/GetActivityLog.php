<?php
namespace NDISmate\Models\ActivityLog;

use \RedBeanPHP\R as R;

class GetActivityLog
{
    public function __invoke($data)
    {
        $bean = R::load('activitylogs', $data['id']);

        return $bean;
    }
}