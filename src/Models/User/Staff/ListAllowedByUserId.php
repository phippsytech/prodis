<?php
namespace NDISmate\Models\User\Staff;

use \RedBeanPHP\R as R;

class ListAllowedByUserId
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll('SELECT 
            userstaffs.staff_id as id, 
            staffs.groups,
                staffs.employment_basis,
                staffs.ordinary_hours_rate,
                staffs.hours_per_week,
                staffs.paygrade_id,
                staffs.archived,
                users.name as staff_name,
                users.first_name,
                users.last_name,
                users.email,
                users.phone,
                staffs.name 
            from userstaffs 
            join staffs on (staffs.id = userstaffs.staff_id) 
            JOIN users on (staffs.user_id = users.id) 
            where userstaffs.user_id=:user_id and userstaffs.allowed=1', [':user_id' => $data['user_id']]);


            // massage the groups array.
            $result = $beans;
            foreach ($result as &$row) {
                $row['groups'] = json_decode($row['groups'], true);
            }
            return $result;

        } catch (\Exception $e) {
            // Throw the exception
            throw new \Exception($e->getMessage());
        }
    }
}

