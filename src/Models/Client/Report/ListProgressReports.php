<?php
namespace NDISmate\Models\Client\Report;

use \RedBeanPHP\R as R;
use \RedBeanPHP\RedException;

class ListProgressReports
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll(
                'SELECT
                    clientreports.id,
                    clients.name, 
                    staffs.name, 
                    clientreports.report_type, 
                    clientreports.due_date,
                    clients.archived,
                    clients.on_hold
                FROM clientreports
                JOIN clientstaffassignments 
                    ON (clientreports.client_id=clientstaffassignments.client_id)
                JOIN clients 
                    ON (clients.id = clientstaffassignments.client_id)
                JOIN staffs 
                    ON (staffs.id = clientstaffassignments.staff_id)
                WHERE clientreports.is_done=0
                AND (clients.archived IS NULL OR clients.archived !=1)
                AND clientstaffassignments.staff_role = :staff_role 
                AND clientstaffassignments.is_primary=1
                AND clientreports.report_type=:report_type
                ORDER BY clientreports.due_date',
                [':staff_role' => $data['staff_role'], ':report_type' => $data['report_type']]
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
