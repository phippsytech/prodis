<?php

namespace NDISmate\Utilities;

use NDISmate\Models\ActivityLog;

class LogActivity
{
    /**
     * Logs an activity in the system.
     *
     * @param array $data The data for logging the activity.
     * @param string $actionType The type of action being logged.
     * @param string $entity The entity related to the action (e.g., 'Client').
     * @param string $reason The reason or description for the action.
     * @param object $guard The guard object containing user context.
     *
     * @return void
     */
    public static function log($data, $actionType, $entity, $reason, $guard)
    {
        // Ensure necessary parameters are available
        if (!isset($data['id']) || !$guard || !isset($guard->user_id)) {
            throw new \InvalidArgumentException('Invalid data or guard provided for logging activity.');
        }

        // Create an activity log entry
        $activityLog = new ActivityLog();
        $activityLog->create([
            'timestamp' => date('Y-m-d H:i:s'),
            'action_type' => $actionType,
            'reason' => $reason,
            'user_id' => $guard->user_id,
            'entity_type' => $entity,
            'entity_id' => $data['id'],
        ]);
    }
}