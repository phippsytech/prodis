<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\SIL\House\Timeline\SaveTimeline;
use NDISmate\Models\SIL\House\Timeline\GetTimeline;
use NDISmate\Models\SIL\House\Timeline\DeleteTimeLine;
use NDISmate\Models\SIL\House\Timeline\GetTimeLineList;
use NDISmate\Models\SIL\House\Timeline\GetTimelineSummary;
use NDISmate\Models\SIL\House\Timeline\GetClientTimelineSummary;
use NDISmate\Models\SIL\House\Timeline\DeleteTimeLine;

final class TimelineController extends BaseController
{
    protected function defineController()
    {
        
        $this->controller = [
            'saveTimeline' => [new SaveTimelines, null, true, []]
            // 'show' => [new GetTimeline, null, true, ['admin']],
            // 'delete' => [new DeleteTimeLine, null, true, ['admin']],
            // 'list' => [new GetTimeLineList, null, true, ['admin']],
            // 'getTimelineSummary' => [new GetTimelineSummary, null, true, ['admin']],
            // 'getClientTimelineSummary' => [new GetClientTimelineSummary, null, true, ['admin']]
        ];
    }
}