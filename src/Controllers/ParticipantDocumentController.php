<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Document\ListDocumentsByParticipantIds;

final class DocumentController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [

            'listDocumentsByParticipantIds' => [new ListDocumentsByParticipantIds, null, true, []],

        ];
    }
}
