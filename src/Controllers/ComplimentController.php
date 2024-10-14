<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Compliment\ListCompliment;
use NDISmate\Models\Register\Compliment;
use NDISmate\Models\Register\Compliment\GetCompliment;

final class ComplimentController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addCompliment' => [new Compliment, 'create', true, []],
            'updateCompliment' => [new Compliment, 'update', true, []],
            'deleteCompliment' => [new Compliment, 'delete', true, []],
            'listCompliments' => [new ListCompliment, null, true, []],
            'getCompliment' => [new GetCompliment, null, true, []]
        ];
    }
}