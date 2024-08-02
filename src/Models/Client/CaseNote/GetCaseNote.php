<?php
namespace NDISmate\Models\Client\CaseNote;

use \RedBeanPHP\R as R;

class GetCaseNote
{
    public function __invoke($data)
    {
        try {
            $bean = R::getRow(
                "SELECT 
                    n.id,
                    n.timetracking_id,
                    n.staff_id,
                    s.name as staff_name,
                    n.client_id,
                    c.name as client_name,
                    n.notes,
                    DATE_FORMAT(COALESCE(t.session_date, n.created), '%Y-%m-%d %H:%i:%s') as created,
                    n.archived,
                    n.internal
                FROM clientcasenotes n
                JOIN staffs s on (s.id = n.staff_id)
                JOIN clients c on (c.id = n.client_id)
                LEFT JOIN timetrackings t ON t.id = n.timetracking_id
                WHERE n.id=:id",
                [
                    ':id' => $data['id']
                ]
            );

            if (!$bean) {
                throw new \Exception('Not Found');
            } else {
                return $bean;
            }
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
