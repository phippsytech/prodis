<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Services\GeoService\SearchAddress;

final class GeoController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'searchAddress' => [new SearchAddress, null, false, []],
        ];
    }
}
