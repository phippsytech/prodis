<?php

namespace NDISmate\Models\Compliment;

use \RedBeanPHP\R as R;

class ListCompliment
{
    public function __invoke($filters = [])
    {
        // Base query with join to get staff name
        $query = '
            SELECT compliments.*, staff.name AS staff_name 
            FROM compliments 
            JOIN staff ON compliments.staff_id = staff.id 
            WHERE 1=1';
        $params = [];

        // Apply any filtering logic (optional)
        if (isset($filters['staff_id'])) {
            $query .= ' AND compliments.staff_id = ?';
            $params[] = $filters['staff_id'];
        }

        // Execute the query and fetch the results
        $compliments = R::getAll($query, $params);

        return $compliments;
    }
}
