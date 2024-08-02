<?php
namespace NDISmate\Models\Client\Report;

use \RedBeanPHP\R as R;
use \RedBeanPHP\RedException;

class ListClientProgressReports
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll(
                "SELECT
                    cr.id,
                    c.id AS client_id,
                    c.name AS client_name, 
                    s.id AS staff_id,
                    s.name AS staff_name, 
                    cr.report_type, 
                    cr.due_date,
                    c.on_hold,
                    (SELECT GROUP_CONCAT(DISTINCT sv.code SEPARATOR ', ')
                    FROM timetrackings tt
                    JOIN services sv ON tt.service_id = sv.id
                    WHERE tt.client_id = c.id 
                    AND tt.staff_id = s.id
                    and sv.code not like '%TRAVEL'
                    GROUP BY tt.client_id, tt.staff_id) AS service_codes
                FROM clientreports cr
                JOIN clientstaffassignments csa ON cr.client_id = csa.client_id
                JOIN clients c ON c.id = csa.client_id
                JOIN staffs s ON s.id = csa.staff_id
                WHERE cr.is_done = 0
                AND DATEDIFF(DATE(cr.due_date), NOW()) <= 21
                AND csa.staff_role != 'dsw' 
                AND (c.archived IS NULL OR c.archived != 1)
                AND cr.report_type IN ('progress')
                ORDER BY cr.due_date"
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
