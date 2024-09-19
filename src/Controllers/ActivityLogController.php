<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\ActivityLog;
use NDISmate\Models\ActivityLog\GetActivityLog;
use NDISmate\Models\ActivityLog\ListActivityLog;
use NDISmate\Models\ActivityLog\GetLatestActivityLog;

final class ActivityLogController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addActivityLog' => [new ActivityLog, 'create', true, []],
            'updateActivityLog' => [new ActivityLog, 'update', true, []],
            'deleteActivityLog' => [new ActivityLog, 'delete', true, []],
            'getActivityLog' => [new GetActivityLog, null, true, []],
            'getLatestActivityLog' => [new GetLatestActivityLog, null, true, []],
            'listActivityLogs' => [new ListActivityLog, null, true, []],
        ];
    }
}