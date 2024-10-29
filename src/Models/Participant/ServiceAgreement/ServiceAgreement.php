<?php

namespace NDISmate\Models\Participant;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

// Service Agreement Statuses
// 1 - Draft - The agreement is being prepared
// 2 - Pending - The agreemement is awaiting approval (a signature)
// 3 - Active - The agreement is in effect and in use.
// 4 - Ended - The agreement is no longer effective, either due to being supeseded, expired or terminated.

class ServiceAgreement extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('serviceagreements'));  // TODO: Migrate to participantserviceagreements
        // parent::__construct($bean ?: R::dispense('participantserviceagreements'));
        $this->fields = [
            'client_id' => [v::numericVal()],  // TODO: Migrate to participant_id
            'service_agreement_signed_date' => [v::optional(v::dateTime('Y-m-d'))],
            'service_agreement_end_date' => [v::optional(v::dateTime('Y-m-d'))],
            'is_active' => [v::optional(v::boolVal())],
            'signatory_name' => [v::optional(v::stringType())],
            'signatory_email' => [v::optional(v::email())],
            'signatory_phone' => [v::optional(v::stringType())],
            'is_draft' => [v::optional(v::boolVal())],
            'parent_id' => [v::optional(v::numericVal())],  // If this service agreement is based on another one, it will have a parent_id
            'status' => [v::optional(v::in(['draft', 'pending', 'active', 'ended']))],
            // 'document_ref' => [v::stringType()], // TODO: relate to stored service agreement document
        ];
    }
}
