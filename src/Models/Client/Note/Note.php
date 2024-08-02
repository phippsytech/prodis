<?php
namespace NDISmate\Models\Client;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class Note extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('clientnotes'));
        $this->fields = [
            'user_id' => [v::numericVal()],
            'client_id' => [v::numericVal()],
            'note' => [v::stringType()],
        ];
    }

    // Additional Methods and overrides here:
    function addNote($data, $fields, $guard)
    {
        $data['user_id'] = $guard->user_id;
        $result = $this->create($data);
        return $result;
    }
}
