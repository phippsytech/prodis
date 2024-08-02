<?php
namespace NDISmate\Models\Client;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class Report extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('clientreports'));
        $this->fields = [
            'client_id' => [v::numericVal()->notEmpty()],
            'report_type' => [v::stringType()->notEmpty()],
            'due_date' => [v::dateTime('Y-m-d')],
            'is_done' => [v::boolType()],
        ];
    }

    public function markDone($data)
    {
        $this->update([
            'id' => $data['id'],
            'is_done' => true,
        ]);
    }

    public function markUndone($data)
    {
        $this->update([
            'id' => $data['id'],
            'is_done' => false,
        ]);
    }
}
