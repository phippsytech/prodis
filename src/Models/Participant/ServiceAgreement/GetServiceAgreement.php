<?php
namespace NDISmate\Models\Participant\ServiceAgreement;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use RedBeanPHP\R as R;

class GetServiceAgreement
{
    public function __invoke($data)
    {
        $bean = R::load('serviceagreements', $data['id']);
        $bean = (new ConvertFieldsToBoolean)($bean, ['is_active']);
        return $bean;
    }
}
