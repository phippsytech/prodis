<?php
namespace NDISmate\Models\ActivityLog;

use \RedBeanPHP\R as R;

class GetLatestActivityLog
{
    public function __invoke($data)
    {
        $query = "SELECT * FROM activitylogs 
                  WHERE entity_type = :entity_type 
                  AND entity_id = :entity_id";
        
        // Check if 'action_type' is provided and add it to the query
        if (!empty($data['action_type'])) {
            $query .= " AND action_type = :action_type";
        }

        // Order by created_at or any other timestamp field to get the latest log
        $query .= " ORDER BY timestamp DESC LIMIT 1";

        // Prepare the parameters for the query
        $params = [
            ':entity_type' => $data['entity_type'],
            ':entity_id' => $data['entity_id']
        ];

        // Add 'action_type' if provided
        if (!empty($data['action_type'])) {
            $params[':action_type'] = $data['action_type'];
        }

        // Execute the query and load the latest activity log
        $bean = R::getRow($query, $params);

        return $bean;
    }
}
