<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\SIL\House\Timeline\SaveTimeline;
// use NDISmate\Models\SIL\House\Timeline\GetTimeline;
// use NDISmate\Models\SIL\House\Timeline\DeleteTimeline;
// use NDISmate\Models\SIL\House\Timeline\GetTimelineList;
// use NDISmate\Models\SIL\House\Timeline\GetTimelineSummary;
// use NDISmate\Models\SIL\House\Timeline\GetClientTimelineSummary;


final class TimelineController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'saveTimeline' => [new SaveTimeline, null, false, [], []]
            // 'show' => [new GetTimeline, null, true, []],
            // 'delete' => [new DeleteTimeline, null, true, []],
            // 'list' => [new GetTimelineList, null, true, []],
            // 'getTimelineSummary' => [new GetTimelineSummary, null, true, []],
            // 'getClientTimelineSummary' => [new GetClientTimelineSummary, null, true, []]
        ];
    }
}