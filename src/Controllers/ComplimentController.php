<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Compliment\ListCompliment;
use NDISmate\Models\Register\Compliment;

final class ComplimentController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addCompliment' => [new Compliment, 'create', true, []],
            'listCompliments' => [new ListCompliment, null, true, []],
        ];
    }
}