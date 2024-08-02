<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\SupportItem\GetSupportItem;
use NDISmate\Models\SupportItem\GetSupportItemByNumber;
use NDISmate\Models\SupportItem\ListSupportItems;
use NDISmate\Models\SupportItem;
use NDISmate\Services\SupportItemService\ImportSupportItem;

final class SupportItemController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addSupportItem' => [new SupportItem, 'create', true, ['admin']],
            'updateSupportItem' => [new SupportItem, 'update', true, ['admin']],
            'listSupportItems' => [new ListSupportItems, null, true, []],
            'getSupportItem' => [new GetSupportItem, null, true, []],
            'getSupportItemByNumber' => [new GetSupportItemByNumber, null, false, []],
            'importSupportItem' => [new ImportSupportItem, null, true, ['admin']]
        ];
    }
}
