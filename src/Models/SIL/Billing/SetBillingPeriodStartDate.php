<?php
namespace NDISmate\Models\SIL\Billing;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class SetBillingPeriodStartDate{

    function __invoke($data){

        // update billing_period_start_date
        KeyValue::set('sil_billing_period_start_date', $data['date']);		


        return ["http_code"=>201, "result"=>$results];

    }

}