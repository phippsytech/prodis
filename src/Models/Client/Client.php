<?php

namespace NDISmate\Models;

use \RedBeanPHP\R as R;
use NDISmate\Models\ActivityLog;
use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use NDISmate\Utilities\LogActivity;

class Client extends NewCustomModel
{
    public function __construct($bean = null)
    {

        parent::__construct($bean ?: R::dispense('clients'));
        $this->fields = [
            'name' => [v::stringType()],
            'date_of_birth' => [v::optional(v::stringType())],  // int(10) unsigned
            'phone_number' => [v::optional(v::stringType())],
            'ndis_number' => [v::stringType()],
            'ndis_plan_start_date' => [v::optional(v::stringType())],
            'ndis_plan_end_date' => [v::optional(v::stringType())],
            'mailing_address' => [v::optional(v::stringType())],  // optional
            'service_provision_location' => [v::optional(v::stringType())],
            'representative_name' => [v::optional(v::stringType())],
            'representative_phone_number' => [v::optional(v::stringType())],
            'representative_email' => [v::optional(v::stringType())],
            'referrer' => [v::optional(v::stringType())],
            'archived' => [v::boolVal()],
            'restrictive_practices' => [v::boolVal()],
            'on_hold' => [v::boolVal()],
            'sil_enabled' => [v::boolVal()],
            'sil_email' => [v::optional(v::stringType())],
            'sil_folder' => [v::optional(v::stringType())],
            'notes' => [v::optional(v::stringType())],
        ];
    }

    public function enableRestrictivePractices($data)
    {
        $this->update([
            'id' => $data['id'],
            'restrictive_practices' => true,
        ]);
    }

    public function disableRestrictivePractices($data)
    {
        $this->update([
            'id' => $data['id'],
            'restrictive_practices' => false,
        ]);
    }

    public function suspendClient($data, $fields, $guard)
    {
        $this->update([
            'id' => $data['id'],
            'on_hold' => true,
        ]);

        // log on hold participant
        LogActivity::log($data['id'], 'on-hold', 'participant', $data['reason'], $guard->user_id);
    }

    public function resumeClient($data, $fields, $guard)
    {
        $this->update([
            'id' => $data['id'],
            'on_hold' => false,
        ]);

        // log resumed participant
        LogActivity::log($data['id'], 'resumed', 'participant', $data['reason'], $guard->user_id);
    }
}
