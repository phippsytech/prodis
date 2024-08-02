<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Client\GetAnonClientServices;
use NDISmate\Models\Client\GetClient;
use NDISmate\Models\Client\GetClientMailingListCSV;
use NDISmate\Models\Client\GetClientReportDueDates;
use NDISmate\Models\Client\ListClients;
use NDISmate\Models\Client\SearchClients;
use NDISmate\Models\Client;
use NDISmate\Services\ParticipantService\ListParticipantsByUserId;

// final class ParticipantController extends BaseController // Final prevents inheritance
class ParticipantController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            // Client Namespace - I ultimately want to move these to the Participant Namespace
            'addClient' => [new Client, 'create', true, ['admin']],
            'updateClient' => [new Client, 'update', true, ['admin']],
            'archiveClient' => [new Client, 'archive', true, ['admin']],
            'restoreClient' => [new Client, 'restore', true, ['admin']],
            'suspendClient' => [new Client, 'suspendClient', true, ['admin']],
            'resumeClient' => [new Client, 'resumeClient', true, ['admin']],
            'enableRestrictivePractices' => [new Client, 'enableRestrictivePractices', true, ['admin']],
            'disableRestrictivePractices' => [new Client, 'disableRestrictivePractices', true, ['admin']],
            'getClient' => [new GetClient, null, true, []],
            'getClientReportDueDates' => [new GetClientReportDueDates, null, true, []],
            'getAnonClientServices' => [new GetAnonClientServices, null, true, ['admin']],
            'getClientMailingList' => [new GetClientMailingListCSV, null, true, ['admin']],
            'listClients' => [new ListClients, null, true, []],
            'searchClients' => [new SearchClients, null, true, []],
            // Participant Namespace
            'listParticipantsByUserId' => [new ListParticipantsByUserId, null, true, []],
        ];
    }
}
