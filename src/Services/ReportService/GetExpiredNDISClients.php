<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class GetExpiredNDISClients
{
    public function __invoke($data)
    {
        $bean = R::getAll(
            "SELECT 
                id,
                name,
                on_hold,
                ndis_plan_end_date
            FROM clients
            WHERE ndis_plan_end_date<FROM_UNIXTIME(UNIX_TIMESTAMP(), '%Y-%m-%d')
            AND (archived != 1 or archived is null)
            "
        );

        return $bean;
    }
}
