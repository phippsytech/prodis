<?php
namespace NDISmate\Models\PlanManager;

use \RedBeanPHP\R as R;


class GetClientsByPlanManagerId{


    function __invoke($data){

        $beans = R::getAll('SELECT DISTINCT clients.name, clients.id 
            FROM clients 
            JOIN serviceagreements ON (clients.id = serviceagreements.client_id)
            JOIN servicebookings ON (serviceagreements.id = servicebookings.plan_id)
            WHERE plan_manager_id=:plan_manager_id',
            [
                ':plan_manager_id' => $data['plan_manager_id']
            ]
        );

        return ["http_code"=>200, "result"=>$beans];

    }


}