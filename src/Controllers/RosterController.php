<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Services\RosterService\GenerateSessions;

final class RosterController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'generateSessions' => [new GenerateSessions, null, true, ['admin']],
        ];
    }
}
