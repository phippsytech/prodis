<?php
namespace NDISmate\Models\PlanManager;

use \RedBeanPHP\R as R;


class GetClientsByPlanManagerId{


    function __invoke($data){

        $beans = R::getAll('SELECT DISTINCT clients.name, clients.id 
            FROM clients 
            JOIN clientplans ON (clients.id = clientplans.client_id)
            JOIN clientplanservices ON (clientplans.id = clientplanservices.plan_id)
            WHERE plan_manager_id=:plan_manager_id',
            [
                ':plan_manager_id' => $data['plan_manager_id']
            ]
        );

        return ["http_code"=>200, "result"=>$beans];

    }


}