<?php
namespace NDISmate\Models\Register\Risk;

use \RedBeanPHP\R as R;

class ListRisk
{
    public function __invoke($data)
    {
            // Initialize an empty array for the result
            $result = [];

            // SQL query to select from the risks table and left join the staffs table
            $sql = "SELECT 
                        risks.*, 
                        staff.name AS staff_name
                    FROM risks
                    LEFT JOIN staffs staff ON risks.staff_id = staff.id";
                
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
            $sql .= ' ORDER BY date_identified DESC';

            // // Execute the query using RedBeanPHP
            $result = R::getAll($sql);

            return $result;
    }
}