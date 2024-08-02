<?php
namespace NDISmate\Models\Client\Plan\Service;

use \RedBeanPHP\R as R;

class GetOne
{
    public function __invoke($data)
    {
        $bean = R::getRow(
            'SELECT 
                clientplanservices.*                
            FROM clientplanservices 
            JOIN services on (services.id = clientplanservices.service_id) 
            JOIN clientplans on (clientplans.id = clientplanservices.plan_id) 
            WHERE clientplans.client_id=:client_id
            AND services.id=:service_id',
            [':client_id' => $data['client_id'], ':service_id' => $data['service_id']]
        );

        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];

        return ['http_code' => 200, 'result' => $bean];
        // return ["http_code"=>200, "result"=>$bean->export()];
    }
}
