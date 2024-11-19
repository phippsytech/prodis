<?php
namespace NDISmate\Models\Register\ConflictOfInterest;


use \RedBeanPHP\R as R;

class ListConflictOfInterest
{
    function __invoke($data) {

        $result = [];

        $sql = "SELECT coi.*, 
            s.name AS staff_name
            FROM conflictofinterests coi
            LEFT JOIN staffs s ON coi.staff_id = s.id";

        $params = [];
        $conditions = [];

        // Build conditions based on provided data if needed (optional)
        // For example, if you want to filter by status or other fields
        if (isset($data['status'])) {
            $conditions[] = 'status = ?';
            $params[] = $data['status'];
        }

        // Append order by clause (optional)
        $sql .= ' ORDER BY date_identified DESC';
        
         // Execute the query using RedBeanPHP
         $result = R::getAll($sql, $params);

        return $result;
    }
}