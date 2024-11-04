<?php
namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class ContinuousImprovement extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('continuousimprovements'));
        $this->fields = [
            'date_of_suggestion' => [v::dateTime('Y-m-d')],
            'suggestion_details' => [v::stringType()],
            'involved_staffs_id' => [v::numericVal()], //involved staff id
            'action_taken' => [v::optional(v::stringType())],
            'status' => [v::stringType()],
            'review_date' => [v::optional(v::dateTime('Y-m-d'))],
            'implementation_date' => [v::optional(v::dateTime('Y-m-d'))],
            'impact_analysis' => [v::optional(v::stringType())],
            'implementing_staffs_id' => [v::optional(v::numericVal())], //implementing staff id
            'completion_date' => [v::optional(v::dateTime('Y-m-d'))],
            'user_id' => [v::numericVal()] // log user id on create
        ];
    }

    public function afterDispense()
    {
        // Set indexes after dispensing the bean
        R::exec("ALTER TABLE compliments ADD INDEX (involved_staffs_id)");
        R::exec("ALTER TABLE compliments ADD INDEX (implementing_staffs_id)");
    }
}
