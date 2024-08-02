<?php
namespace NDISmate\Models\SIL\Billing;

use NDISmate\CORE\CRUD;
use NDISmate\CORE\JsonResponse;
use NDISmate\CORE\KeyValue;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class GetBillingPeriodStartDate
{
    function __invoke()
    {
        // update billing_period_start_date
        $start_date = KeyValue::get('sil_billing_period_start_date');

        return $start_date;
    }
}
