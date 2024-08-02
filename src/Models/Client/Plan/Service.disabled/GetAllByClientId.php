<?php
namespace NDISmate\Models\Client\Plan\Service;

use \RedBeanPHP\R as R;

class GetAllByClientId
{
    public function __invoke($data)
    {
        $bean = R::getAll(
            'SELECT 
                clientplanservices.id as id,
                clientplanservices.rate as service_rate,
                services.id as service_id,
                services.code as service_code,
                services.name as services_name,
                clientplanservices.budget as budget,
                clientplanservices.kilometer_budget as kilometer_budget
            FROM clientplanservices 
            JOIN services on (services.id = clientplanservices.service_id) 
            JOIN clientplans on (clientplans.id = clientplanservices.plan_id) 
            WHERE clientplans.is_active=1 AND clientplans.client_id=:client_id',
            [':client_id' => $data['client_id']]
        );

        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];

        return ['http_code' => 200, 'result' => $bean];
        // return ["http_code"=>200, "result"=>$bean->export()];
    }
}
