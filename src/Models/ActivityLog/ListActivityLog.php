<?php
namespace NDISmate\Models\ActivityLog;

use \RedBeanPHP\R as R;

class ListActivityLog
{
    public function __invoke($data)
    {
        // Initialize an empty array for the result
        $result = [];

        // Initialize base SQL query with join to include user_name from users table
        $sql = "SELECT activitylogs.*, users.name AS user_name 
                FROM activitylogs 
                LEFT JOIN users ON activitylogs.user_id = users.id";
        
        $params = [];
        $conditions = [];

        // Build conditions based on provided data
        if (isset($data['entity_type'])) {
            $conditions[] = 'entity_type = ?';
            $params[] = $data['entity_type'];
        }
        if (isset($data['entity_id'])) {
            $conditions[] = 'entity_id = ?';
            $params[] = $data['entity_id'];
        }
        if (isset($data['action_type'])) {
            $conditions[] = 'action_type = ?';
            $params[] = $data['action_type'];
        }

        // Append conditions to SQL query
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Append order by clause
        $sql .= ' ORDER BY timestamp DESC';

        // Execute the query using RedBeanPHP
        $result = R::getAll($sql, $params);

        return $result;
    }
}
