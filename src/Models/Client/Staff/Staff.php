<?php
namespace NDISmate\Models\Client;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class Staff extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('clientstaffassignments'));
        $this->fields = [
            'client_id' => [v::numericVal()->notEmpty()],
            'staff_id' => [v::numericVal()->notEmpty()],
            'staff_role' => [v::stringType()->notEmpty()],
            'is_primary' => [v::boolType()],
        ];
    }

    public function create($data)
    {
        $bean = parent::create($data);

        // get the staff name using staff_id and add it to the bean that is returned
        $staff = R::load('staffs', $data['staff_id']);
        $bean->staff_name = $staff->name;
        $client = R::load('clients', $data['client_id']);
        $bean->client_name = $client->name;
        return $bean;
    }

    public function makePrimary($data)
    {
        $clientstaffassignment = $this->load($data['id']);

        $clientId = $clientstaffassignment->client_id;
        $staffRole = $clientstaffassignment->staff_role;

        // Set is_primary to false for all records with the same client_id and staff_role
        R::exec(
            'UPDATE clientstaffassignments 
            SET is_primary = 0 
            WHERE client_id = :client_id 
            AND staff_role = :staff_role',
            [':client_id' => $clientId, ':staff_role' => $staffRole]
        );

        // Set is_primary to true for the current record
        $result = $this->update([
            'id' => $data['id'],
            'is_primary' => true,
        ]);

        return $result;
    }

    public function removePrimary($data)
    {
        $this->update([
            'id' => $data['id'],
            'is_primary' => false,
        ]);
    }
}
