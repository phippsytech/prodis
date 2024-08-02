<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Services\ReportService\GetCapacity;
use NDISmate\Services\ReportService\GetClientsWithoutAgreement;
use NDISmate\Services\ReportService\GetExpiredNDISClients;
use NDISmate\Services\ReportService\GetExpiredServiceAgreements;
use NDISmate\Services\ReportService\GetLastSeen;
use NDISmate\Services\ReportService\GetOverdueVisits;
use NDISmate\Services\ReportService\GetParticipantBudget;

final class ReportController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'getExpiredNDISClients' => [new GetExpiredNDISClients, null, true, ['admin']],
            'getClientsWithoutAgreement' => [new GetClientsWithoutAgreement, null, true, ['admin']],
            'getOverdueVisits' => [new GetOverdueVisits, null, false, ['admin']],
            'getParticipantBudget' => [new GetParticipantBudget, null, true, ['admin']],
            'getCapacity' => [new GetCapacity, null, true, ['admin']],
            'getLastSeen' => [new GetLastSeen, null, false, ['admin']],
            'getExpiredServiceAgreements' => [new GetExpiredServiceAgreements, null, true, ['admin']],
        ];
    }
}
