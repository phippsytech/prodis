<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Client\CaseNote\GetCaseNote;
use NDISmate\Models\Client\CaseNote\ListCaseNotesByClientId;
use NDISmate\Models\Client\CaseNote\ListCaseNotesByStaffId;
use NDISmate\Models\Client\CaseNote;

final class ClientCaseNoteController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addCaseNote' => [new CaseNote, 'create', true, ['therapist', 'admin']],
            'updateCaseNote' => [new CaseNote, 'updateCaseNote', true, ['therapist', 'admin']],
            'listCaseNotesByClientId' => [new ListCaseNotesByClientId, null, true, ['therapist', 'stakeholder', 'admin']],
            'listCaseNotesByStaffId' => [new listCaseNotesByStaffId, null, false, ['therapist', 'stakeholder', 'admin']],
            'getCaseNote' => [new GetCaseNote, null, true, []],
        ];
    }
}
