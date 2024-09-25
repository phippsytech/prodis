<?php
namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Training extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('trainings'));
        $this->fields = [
            'date' => [v::dateTime('Y-m-d')],
            'course_title' => [v::stringType()],
            'trainer' => [v::stringType()],
            'status' => [v::stringType()],
            'completion_date' => [v::optional(v::dateTime('Y-m-d'))],
            'has_evidence' => [v::boolVal()],
        ];
    }

    public function create($data)
    {
        $bean = parent::create($data);
        // fetch
        // $data[staf_ids]
        // loop through staff_ids

        return $bean;
    }

    public function update($data)
    {
        $bean = parent::update($data);
        // fetch
        // $data[staf_ids]
        // loop through staff_ids

        return $bean;
    }
}