<?php
namespace NDISmate\Models\ActivityLog;

use \RedBeanPHP\R as R;

class ListActivityLog
{
    public function __invoke($data)
    {
        // Initialize an empty array for the result
        $result = [];

        // Check conditions and fetch data using RedBeanPHP
        if (isset($data['entity_type']) && isset($data['entity_id']) && isset($data['action_type'])) {
            $beans = R::findAll(
                'activitylogs',
                'entity_type = ? AND entity_id = ? AND action_type = ? ORDER BY timestamp',
                [$data['entity_type'], $data['entity_id'], $data['action_type']]
            );
        } elseif (isset($data['entity_type']) && isset($data['entity_id'])) {
            $beans = R::findAll(
                'activitylogs',
                'entity_type = ? AND entity_id = ? ORDER BY timestamp',
                [$data['entity_type'], $data['entity_id']]
            );
        } elseif (isset($data['action_type'])) {
            $beans = R::findAll(
                'activitylogs',
                'action_type = ? ORDER BY timestamp',
                [$data['action_type']]
            );
        } else {
            $beans = R::findAll('activitylogs', 'ORDER BY timestamp');
        }

        // Convert beans to an array collection
        foreach ($beans as $bean) {
            $result[] = $bean->export(); // Convert each bean to an array
        }

        return $result;
    }
}