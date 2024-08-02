<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Client\Report\ListClientProgressReports;
use NDISmate\Models\Client\Report\ListClientReports;
use NDISmate\Models\Client\Report\ListClientReportsByClientId;
use NDISmate\Models\Client\Report\ListClientReportsByStaffId;
use NDISmate\Models\Client\Report as ClientReport;

final class ClientReportController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addClientReport' => [new ClientReport, 'create', true, ['admin']],
            'updateClientReport' => [new ClientReport, 'update', true, ['admin']],
            'deleteClientReport' => [new ClientReport, 'delete', true, ['admin']],
            'markDone' => [new ClientReport, 'markDone', true, ['admin']],
            'markUndone' => [new ClientReport, 'markUndone', true, ['admin']],
            'listClientReports' => [new ListClientReports, null, false, []],
            'listClientProgressReports' => [new ListClientProgressReports, null, false, []],
            'listClientReportsByClientId' => [new ListClientReportsByClientId, null, false, []],
            'listClientReportsByStaffId' => [new ListClientReportsByStaffId, null, false, ['therapist']],
        ];
    }
}
