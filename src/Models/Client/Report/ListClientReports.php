<?php
namespace NDISmate\Models\Client\Report;

use NDISmate\Models\Client\Report\ListClientBSPReports;
use NDISmate\Models\Client\Report\ListClientOTReports;
use NDISmate\Models\Client\Report\ListClientProgressReports;

class ListClientReports
{
    public function __invoke($data)
    {
        try {
            $bspReports = new ListClientBSPReports();
            $otReports = new ListClientOTReports();
            $progressReports = new ListClientProgressReports();

            $clientReports = array_merge($bspReports($data), $otReports($data), $progressReports($data));

            // now sort the reports by due date
            usort($clientReports, function ($a, $b) {
                return strtotime($a['due_date']) - strtotime($b['due_date']);
            });

            return $clientReports;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
