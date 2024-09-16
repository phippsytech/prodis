<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class ActivityLog extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('activitylogs'));
        $this->fields = [
            'timestamp' => [v::dateTime('Y-m-d H:i:s')],  // Including time in the timestamp
            'action_type' => [v::stringType()],  // Action type is required
            'reason' => [v::optional(v::stringType())],  // Nullable text field for the reason
            'user_id' => [v::numericVal()],  // Foreign key for the user who performed the action
            'entity_type' => [v::stringType()],  // Should be set to "Client" for client-related actions
            'entity_id' => [v::numericVal()],  // ID of the affected client
        ];
    }

    public function afterDispense()
    {
        // Set indexes after dispensing the bean
        R::exec("ALTER TABLE activitylogs ADD INDEX (timestamp)");
        R::exec("ALTER TABLE activitylogs ADD INDEX (user_id)");
        R::exec("ALTER TABLE activitylogs ADD INDEX (entity_type, entity_id)");
    }
}
