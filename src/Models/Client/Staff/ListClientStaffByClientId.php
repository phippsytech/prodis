<?php
namespace NDISmate\Models\Client\Staff;

use \RedBeanPHP\R as R;
use \RedBeanPHP\RedException;

class ListClientStaffByClientId
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll(
                'SELECT 
                    csa.id, 
                    csa.staff_id, 
                    staffs.name as staff_name,
                    csa.staff_role, 
                    csa.is_primary
                FROM clientstaffassignments csa
                JOIN staffs 
                    ON staffs.id = csa.staff_id
                WHERE 
                    (staffs.archived IS NULL OR staffs.archived != 1)
                    AND csa.client_id = :client_id',
                [':client_id' => $data['client_id']]
            );

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
