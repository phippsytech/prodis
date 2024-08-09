<?php
namespace NDISmate\Models\Participant;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class Service extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('clientplanservices'));  // TODO: Migrate to participantservices
        // parent::__construct($bean ?: R::dispense('participantservices'));
        $this->fields = [
            'participant_id' => [v::numericVal()],
            'plan_id' => [v::numericVal()],  // TODO: migrate to service_agreement_id
            'service_id' => [v::numericVal()],
            'plan_manager_id' => [v::numericVal()],
            'budget' => [v::monetaryAmount()],
            'budget_start_date' => [v::date('Y-m-d')],
            'rate' => [v::optional(v::monetaryAmount())],
            'include_travel' => [v::boolVal()],
            'kilometer_budget' => [v::optional(v::monetaryAmount())],
            'max_claimable_travel_duration' => [v::optional(v::numericVal())],
            'xero_account_code' => [v::optional(v::stringType())],
            'is_active' => [v::boolVal()],
        ];
    }
}
