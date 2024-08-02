<?php
namespace NDISmate\Models\Client;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class CaseNote extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('clientcasenotes'));
        $this->fields = [
            'timetracking_id' => [v::optional(v::numericVal())],  // optional link to timetracking data
            'client_id' => [v::optional(v::numericVal())],
            'staff_id' => [v::optional(v::numericVal())],
            'notes' => [v::optional(v::stringType())],  // eg The Case Note
            'internal' => [v::optional(v::boolVal())],  // if the case note is internal only
        ];
    }

    function updateCaseNote($data)
    {
        $result = $this->update($data);
        return $result;
    }
}
