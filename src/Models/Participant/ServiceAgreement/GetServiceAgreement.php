<?php

namespace NDISmate\Models\Participant\ServiceAgreement;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use RedBeanPHP\R as R;

class GetServiceAgreement
{
    public function __invoke($data)
    {

        $bean = R::load('serviceagreements', $data['id'])->export();

        if (!$bean) {
            throw new \Exception('Service Agreement Not Found', 404);
        } else {
            // We need to pass $bean as an array because it's the converter is expecting an array of beans
            $bean = (new ConvertFieldsToBoolean)([$bean], ['is_active', 'is_draft']);
            return $bean[0];
        }
    }
}
