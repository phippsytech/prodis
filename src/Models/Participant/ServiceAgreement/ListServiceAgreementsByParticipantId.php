<?php
namespace NDISmate\Models\Participant\ServiceAgreement;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use RedBeanPHP\R as R;

class ListServiceAgreementsByParticipantId
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            'SELECT *
            FROM serviceagreements
            WHERE client_id = :participant_id',
            [':participant_id' => $data['participant_id']]
        );
        $result = (new ConvertFieldsToBoolean)($beans, ['is_active']);

        return $result;
    }
}
