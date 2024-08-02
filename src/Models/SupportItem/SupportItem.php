<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class SupportItem extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('supportitems'));
        $this->fields = [
            'support_item_number' => [v::stringType()],
            'support_item_name' => [v::stringType()],
            'registration_group_number' => [v::stringType()],
            'registration_group_name' => [v::stringType()],
            'support_category_number' => [v::stringType()],
            'support_category_number_pace' => [v::stringType()],
            'support_category_name' => [v::stringType()],
            'support_category_name_pace' => [v::stringType()],
            'unit' => [v::stringType()],
            'quote' => [v::stringType()],
            'start_date' => [v::stringType()],
            'end_date' => [v::stringType()],
            'act' => [v::monetaryAmount()],
            'nsw' => [v::monetaryAmount()],
            'nt' => [v::monetaryAmount()],
            'qld' => [v::monetaryAmount()],
            'sa' => [v::monetaryAmount()],
            'tas' => [v::monetaryAmount()],
            'vic' => [v::monetaryAmount()],
            'wa' => [v::monetaryAmount()],
            'remote' => [v::monetaryAmount()],
            'very_remote' => [v::monetaryAmount()],
            'non_face_to_face_support_provision' => [v::stringType()],
            'provider_travel' => [v::stringType()],
            'short_notice_cancellations' => [v::stringType()],
            'ndia_requested_reports' => [v::stringType()],
            'irregular_sil_supports' => [v::stringType()],
            'type' => [v::stringType()]
        ];
    }
}
