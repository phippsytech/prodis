<?php
namespace NDISmate\Models\Participant;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class ServiceAgreement extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('serviceagreements'));  // TODO: Migrate to participantserviceagreements
        // parent::__construct($bean ?: R::dispense('participantserviceagreements'));
        $this->fields = [
            'client_id' => [v::numericVal()],  // TODO: Migrate to participant_id
            'service_agreement_signed_date' => [v::dateTime('Y-m-d')],
            'service_agreement_end_date' => [v::dateTime('Y-m-d')],
            'is_active' => [v::boolVal()],
            'is_amendment' => [v::boolVal()],
            'parent_id' => [v::optional(v::numericVal())],  // If is_amendment, this is the parent service agreement id
            // 'document_ref' => [v::stringType()], // TODO: relate to stored service agreement document
        ];
    }
}
