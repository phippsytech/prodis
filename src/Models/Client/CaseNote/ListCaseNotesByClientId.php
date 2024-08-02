<?php
namespace NDISmate\Models\Client\CaseNote;

use \RedBeanPHP\R as R;

class ListCaseNotesByClientId
{
    public function __invoke($data, $fields, $guard)
    {
        try {
            $user_roles = $guard->roles;

            $sql =
                "SELECT 
                    n.id,
                    n.timetracking_id,
                    n.staff_id,
                    s.name as staff_name,
                    n.notes,
                    DATE_FORMAT(COALESCE(t.session_date, n.created), '%Y-%m-%d %H:%i:%s') as created,
                    t.session_duration,
                    n.archived
                FROM clientcasenotes n
                JOIN staffs s on (s.id = n.staff_id)
                LEFT JOIN timetrackings t ON t.id = n.timetracking_id
                WHERE n.client_id=:client_id";

            // if the user is a stakeholder, they can only see case notes where internal is false or not set
            if (count(array_intersect($user_roles, ['therapist', 'admin'])) === 0) {
                $sql .= ' AND (n.internal != 1 OR n.internal IS NULL)';
            }

            $sql .= ' ORDER BY created DESC';

            $bean = R::getAll($sql, [':client_id' => $data['client_id']]);

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
