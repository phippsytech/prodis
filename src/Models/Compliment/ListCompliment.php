<?php

namespace NDISmate\Models\Compliment;

use \RedBeanPHP\R as R;

class ListCompliment
{
    public function __invoke($filters = [])
    {
        // Example of applying filters if needed
        $query = 'SELECT * FROM compliments WHERE 1=1';
        $params = [];

        // Apply any filtering logic (optional)
        if (isset($filters['staff_id'])) {
            $query .= ' AND staff_id = ?';
            $params[] = $filters['staff_id'];
        }

        // Execute the query and fetch the results
        $compliments = R::getAll($query, $params);

        return $compliments;
    }
}
