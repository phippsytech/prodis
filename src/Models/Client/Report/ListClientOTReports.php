<?php
namespace NDISmate\Models\Client\Report;

use \RedBeanPHP\R as R;
use \RedBeanPHP\RedException;

class ListClientOTReports
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll(
                "SELECT
                    clientreports.id,
                    clients.id as client_id,
                    clients.name as client_name, 
                    staffs.id as staff_id,
                    staffs.name as staff_name, 
                    clientreports.report_type, 
                    clientreports.due_date,
                    clients.on_hold
                FROM clientreports
                JOIN clientstaffassignments 
                    ON (clientreports.client_id=clientstaffassignments.client_id)
                JOIN clients 
                    ON (clients.id = clientstaffassignments.client_id)
                JOIN staffs 
                    ON (staffs.id = clientstaffassignments.staff_id)
                WHERE clientreports.is_done=0
                AND DATEDIFF(DATE(clientreports.due_date), NOW()) <=21
                AND (clients.archived IS NULL OR clients.archived !=1)
                AND clientstaffassignments.staff_role = 'ot' 
                AND clientstaffassignments.is_primary=1
                AND clientreports.report_type IN ('fca')
                ORDER BY clientreports.due_date"
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
