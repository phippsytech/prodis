<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Task extends NewCustomModel
{
    public function __construct($bean = null)
    {

        parent::__construct($bean ?: R::dispense('tasks'));
        $this->fields = [
            'title' => [v::stringType()],  // The name of the task.
            'description' => [v::optional(v::stringType())],  // A detailed description of the task.
            'user_id' => [v::numericVal()],  // The ID of the user who created the task.

            // match this with however MySQL stores date time.
            'due_date' => [v::optional(v::dateTime('Y-m-d H:i'))],  // The due date for the task.
            
            'status' => [v::optional(v::stringType())],  // The status of the task (e.g., "pending," "in progress," "completed," "overdue").
            'priority' => [v::optional(v::stringType())],  // The priority of the task (e.g., "low," "normal," "high," "urgent"). Using a number would help order tasks.
            'assigned_to' => [v::optional(v::numericVal())],  // Can be either a user or a group.
            'assigned_by' => [v::optional(v::numericVal())],  // The user who assigned the task.
            'in_progress_at' => [v::optional(v::dateTime('Y-m-d H:i'))], // Timestamp when the task was marked as "in progress."
            'in_progress_by' => [v::optional(v::numericVal())],  // The user who marked the task as "in progress."
            // 'in_progress_by' => [v::optional(v::stringType())],  // The user who marked the task as "in progress."
        ];
    }
}
