<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Client\Stakeholder\GetStakeholder;
use NDISmate\Models\Client\Stakeholder\ListStakeholderClientsByUserId;
use NDISmate\Models\Client\Stakeholder\ListStakeholders;
use NDISmate\Models\Client\Stakeholder\ListStakeholdersByClientId;
use NDISmate\Models\Client\Stakeholder;

final class ClientStakeholderController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addStakeholder' => [new Stakeholder, 'create', true, ['therapist', 'participant.modify', 'admin']],
            'updateStakeholder' => [new Stakeholder, 'update', true, ['therapist', 'participant.modify', 'admin']],
            'deleteStakeholder' => [new Stakeholder, 'delete', true, ['therapist', 'participant.modify', 'admin']],
            'archiveStakeholder' => [new Stakeholder, 'archive', true, ['therapist', 'participant.modify', 'admin']],
            'restoreStakeholder' => [new Stakeholder, 'restore', true, ['therapist', 'participant.modify', 'admin']],
            'makeRepresentative' => [new Stakeholder, 'makeRepresentative', true, ['therapist', 'participant.modify', 'admin']],
            'removeRepresentative' => [new Stakeholder, 'removeRepresentative', true, ['therapist', 'participant.modify', 'admin']],
            'getStakeholder' => [new GetStakeholder, null, true, []],
            'listStakeholders' => [new ListStakeholders, null, true, []],
            'listStakeholdersByClientId' => [new ListStakeholdersByClientId, null, true, []],
            'listStakeholderClientsByUserId' => [new ListStakeholderClientsByUserId, null, true, []],
        ];
    }
}
