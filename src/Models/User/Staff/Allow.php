<?php
namespace NDISmate\Models\User\Staff;

use \RedBeanPHP\R as R;

class Allow
{
    public function __invoke($data)
    {
        try {
            $bean = R::findOrCreate('userstaffs', ['user_id' => $data['user_id'], 'staff_id' => $data['staff_id']]);
            $bean->allowed = 1;
            R::store($bean);

            return true;
        } catch (\Exception $e) {
            // Throw the exception
            throw new \Exception($e->getMessage());
        }
    }
}
