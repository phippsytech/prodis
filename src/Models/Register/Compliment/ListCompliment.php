<?php
namespace NDISmate\Models\Register\Compliment;

use \RedBeanPHP\R as R;

class ListCompliment
{
    public function __invoke($data)
    {
        // Initialize an empty array for the result
        $result = [];

        // SQL query to select compliments along with staff names
        $sql = "SELECT c.*, s.name AS staff_name 
                FROM compliments c 
                LEFT JOIN staffs s ON c.staffs_id = s.id"; // Join to get staff names
        
        $params = [];
        $conditions = [];

        // Build conditions based on provided data if needed (optional)
        if (isset($data['status'])) {
            $conditions[] = 'c.status = ?';
            $params[] = $data['status'];
        }

        // Append conditions to SQL query if there are any
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Append order by clause (optional)
        $sql .= ' ORDER BY c.date DESC';

        // Execute the query using RedBeanPHP
        $result = R::getAll($sql, $params);

        return $result;
    }
}
