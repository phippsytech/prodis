<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\TimeTracking\GetTimeTracking;
use NDISmate\Models\TimeTracking\ListTimeTrackingByClientId;
use NDISmate\Models\TimeTracking\ListTimeTrackingByStaffId;
use NDISmate\Models\TimeTracking\ListTimeTrackings;
use NDISmate\Services\TimeTrackingService\AddTimeTracking;
use NDISmate\Services\TimeTrackingService\DeleteTimeTracking;
use NDISmate\Services\TimeTrackingService\GetMinutes;
use NDISmate\Services\TimeTrackingService\GetSummary;
use NDISmate\Services\TimeTrackingService\UpdateTimeTracking;

final class TimeTrackingController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addTimeTracking' => [new AddTimeTracking, null, true, []],
            'deleteTimeTracking' => [new DeleteTimeTracking, null, true, []],
            'getTimeTracking' => [new GetTimeTracking, null, true, []],
            'getMinutes' => [new GetMinutes, null, true, []],
            'getTimeTrackingSummary' => [new GetSummary, null, false, []],
            'listTimeTracking' => [new ListTimeTrackings, null, true, []],
            'listTimeTrackingByClientId' => [new ListTimeTrackingByClientId, null, false, []],
            'listTimeTrackingByStaffId' => [new ListTimeTrackingByStaffId, null, false, []],
            'updateTimeTracking' => [new UpdateTimeTracking, null, true, []],
        ];
    }
}
