<?php

namespace NDISmate\Models\Client\Staff;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use \RedBeanPHP\R as R;
use \RedBeanPHP\RedException;

class ListStaffClientsByStaffId
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll(
                'SELECT 
                    csa.id, 
                    csa.client_id, 
                    clients.name as client_name,
                    clients.on_hold,
                    csa.staff_role, 
                    csa.is_primary
                FROM clientstaffassignments csa
                JOIN clients 
                    ON clients.id = csa.client_id
                WHERE 
                    (clients.archived IS NULL OR clients.archived != 1)
                    AND csa.staff_id = :staff_id
                ORDER BY client_name ASC',
                [':staff_id' => $data['staff_id']]
            );

            $beans = (new ConvertFieldsToBoolean)($beans, ['on_hold']);

            return $beans;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
