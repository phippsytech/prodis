<?php
namespace NDISmate\Models\Client;

use \RedBeanPHP\R as R;

class GetClientReportDueDates
{
    public function __invoke($data)
    {
        $bean = R::getAll('SELECT 
        client_id,
        UNIX_TIMESTAMP(DATE_ADD(DATE(FROM_UNIXTIME(service_agreement_signed_date)), INTERVAL 4 WEEK)) AS interim_date,
        UNIX_TIMESTAMP(DATE_ADD(DATE(FROM_UNIXTIME(service_agreement_signed_date)), INTERVAL 6 MONTH)) AS comprehensive_date,
        UNIX_TIMESTAMP(DATE_ADD(DATE(FROM_UNIXTIME(service_agreement_signed_date)), INTERVAL 18 MONTH)) AS review_date
        FROM clientplans
        WHERE
        client_id=:client_id ', [':client_id' => $data['client_id']]);

        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];

        return $bean;
    }
}
