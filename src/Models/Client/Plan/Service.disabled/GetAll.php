<?php
namespace NDISmate\Models\Client\Plan\Service;

use \RedBeanPHP\R as R;

class GetAll
{
    public function __invoke($data)
    {
        $bean = R::getAll('SELECT 
        
        clientplanservices.id as id,
        services.id as service_id,
        services.code as service_code,
        services.name as services_name,
        clientplanservices.budget as budget,
        clientplanservices.kilometer_budget as kilometer_budget

        
         from clientplanservices join services on (services.id = clientplanservices.service_id) where plan_id=:plan_id ', [':plan_id' => $data['plan_id']]);

        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];

        return ['http_code' => 200, 'result' => $bean];
        // return ["http_code"=>200, "result"=>$bean->export()];
    }
}
