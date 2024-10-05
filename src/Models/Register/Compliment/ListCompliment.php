<?php
namespace NDISmate\Models\Register\Compliment;

use \RedBeanPHP\R as R;

class ListCompliment
{
    public function __invoke($data)
    {
            // Initialize an empty array for the result
            $result = [];

            // Basic SQL query to select all from the trainings table
            $sql = "SELECT * FROM compliments";
            
            $params = [];
            $conditions = [];

            // Build conditions based on provided data if needed (optional)
            // For example, if you want to filter by status or other fields
            if (isset($data['status'])) {
                $conditions[] = 'status = ?';
                $params[] = $data['status'];
            }

            // Append conditions to SQL query if there are any
            if (!empty($conditions)) {
                $sql .= ' WHERE ' . implode(' AND ', $conditions);
            }

            // Append order by clause (optional)
            $sql .= ' ORDER BY date DESC';

            // Execute the query using RedBeanPHP
            $result = R::getAll($sql, $params);

            return $result;
    }
}