<?php
namespace NDISmate\Models\Client;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Stakeholder extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('stakeholders'));
        $this->fields = [
            'client_id' => [v::numericVal()],
            'user_id' => [v::optional(v::numericVal())],
            'name' => [v::stringType()],
            'relationship' => [v::optional(v::stringType())],
            'email' => [v::stringType()],
            'phone' => [v::optional(v::stringType())],
            'notes' => [v::optional(v::stringType())],
            'representative' => [v::boolVal()],
        ];
    }

    public function makeRepresentative($data)
    {
        $stakeholder = $this->load($data['id']);

        // Set is_primary to false for all records with the same client_id and staff_role
        R::exec(
            'UPDATE stakeholders 
            SET representative = false 
            WHERE id != :stakeholder_id',
            [':stakeholder_id' => $data['id']]
        );

        // Set is_primary to true for the current record
        $result = $this->update([
            'id' => $data['id'],
            'representative' => true,
        ]);

        return $result;
    }

    public function removeRepresentative($data)
    {
        $this->update([
            'id' => $data['id'],
            'representative' => false,
        ]);
    }
}
