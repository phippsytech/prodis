<?php
namespace NDISmate\Models\Register\Training;

use \RedBeanPHP\R as R;

class GetTrainingAssignees
{
    public function __invoke($data)
    {
        // Initialize an empty array for the result
        $result = [];

        // Basic SQL query to select staff_id from trainingassignees based on training_id
        $sql = "SELECT staff_id FROM trainingassignees WHERE training_id = ?";
        
        // Check if training_id is provided in the input data
        if (!isset($data['training_id'])) {
            throw new \InvalidArgumentException("training_id must be provided");
        }

        // Prepare parameters
        $params = [$data['training_id']];

        // Execute the query using RedBeanPHP
        $result = R::getAll($sql, $params);

        // Extract staff IDs from the result
        $staffIds = array_column($result, 'staff_id');

        return $staffIds; // Return only the staff IDs
    }
}
