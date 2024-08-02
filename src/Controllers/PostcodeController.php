<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Services\PostcodeService\Import;
use NDISmate\Services\PostcodeService\Lookup;

final class PostcodeController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'lookup' => [new Lookup, null, false, []],
            'import' => [new Import, null, true, ['admin']],
        ];
    }
}
