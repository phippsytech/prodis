<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Compliment;
use NDISmate\Models\Compliment\GetCompliment;
use NDISmate\Models\Compliment\ListCompliment;

final class ComplimentController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addCompliment' => [new Compliment, 'create', true, []],
            'updateCompliment' => [new Compliment, 'update', true, []],
            'deleteCompliment' => [new Compliment, 'delete', true, []],
            'getCompliment' => [new GetCompliment, null, true, []],
            'listCompliment' => [new ListCompliment, null, true, []],
        ];
    }
}