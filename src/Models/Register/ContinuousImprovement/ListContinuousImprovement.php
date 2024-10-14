<?php
namespace NDISmate\Models\Register\ContinuousImprovement;

use \RedBeanPHP\R as R;

class ListContinuousImprovement 
{
    public function __invoke($data)
    {
        // Initialize an empty array for the result
        $result = [];

        // SQL query to select continuous improvements along with staff names
        $sql = "SELECT ci.*, 
                       involved_staff.name AS involved_staff_name,
                       implementing_staff.name AS implementing_staff_name
                FROM continuousimprovements ci
                LEFT JOIN staffs involved_staff ON ci.involved_staffs_id = involved_staff.id
                LEFT JOIN staffs implementing_staff ON ci.implementing_staffs_id = implementing_staff.id"; // Join to get staff names

        $params = [];
        $conditions = [];

        // Build conditions based on provided data if needed (optional)
        if (isset($data['status'])) {
            $conditions[] = 'ci.status = ?';
            $params[] = $data['status'];
        }

        // Append conditions to SQL query if there are any
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Append order by clause (optional)
        $sql .= ' ORDER BY ci.date_of_suggestion DESC';

        // Execute the query using RedBeanPHP
        $result = R::getAll($sql, $params);

        return $result;
    }
}