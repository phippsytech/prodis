<?php
namespace NDISmate\Models\Client\CaseNote;

use \RedBeanPHP\R as R;

class ListCaseNotesByStaffId
{
    public function __invoke($data)
    {
        try {
            $bean = R::getAll(
                'SELECT 
                    n.id,
                    n.timetracking_id,
                    n.client_id,
                    c.name as client_name,
                    n.notes,
                    n.created,                
                    n.archived
                FROM clientcasenotes n
                JOIN clients c on (c.id = n.client_id)
                WHERE n.staff_id=:staff_id
                ORDER BY n.created DESC',
                [
                    ':staff_id' => $data['staff_id']
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
