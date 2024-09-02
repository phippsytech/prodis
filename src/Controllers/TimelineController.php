<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\SIL\House\Timeline\SaveTimeline;
use NDISmate\Models\SIL\House\Timeline\GetTimeline;
use NDISmate\Models\SIL\House\Timeline\DeleteTimeLine;

final class TimelineController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'save' => [new SaveTimeline, null, true, ['admin']],
            'show' => [new GetTimeline, null, true, ['admin']],
            'delete' => [new DeleteTimeLine, null, true, ['admin']],
        ]
    }
}