<?php
namespace NDISmate\Models\User\Staff;

use \RedBeanPHP\R as R;

class Delete
{
    public function __invoke($data)
    {
        try {
            $bean = R::findOne('userstaffs', ' user_id=:user_id AND staff_id=:staff_id AND allowed=:allowed ', [
                ':user_id' => $data['user_id'],
                ':staff_id' => $data['staff_id'],
                ':allowed' => $data['allowed']
            ]);
            if ($bean)
                R::trash($bean);

            return true;
        } catch (\Exception $e) {
            // Throw the exception
            throw new \Exception($e->getMessage());
        }
    }
}
