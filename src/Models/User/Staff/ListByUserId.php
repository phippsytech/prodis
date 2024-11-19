<?php
namespace NDISmate\Models\User\Staff;

use \RedBeanPHP\R as R;

class ListByUserId
{
    public function __invoke($data)
    {
        if (!isset($data['user_id'])) {
            return ['http_code' => 400, 'error' => 'user_id is required'];
        }

        // this function returns an array of staff_ids that the user has access to

        $staff_ids = [];  // default is empty array (NO access to any staffs)

        // look up user by id to see if they have full permission
        $access_all_staffs = R::getCell('SELECT access_all_staffs from users where id=:id', [':id' => $data['user_id']]);

        if ($access_all_staffs) {
            // if user can access all staffs get all staff_ids
            $staffs = R::getAll(
                'SELECT staff_id from staffs'
            );
        } else {
            // if user can't access all staffs

            // get staff_ids from UserStaffAccess table
            // are they staff?
            $staff = R::getRow('SELECT id from staffs where user_id=:user_id', [':user_id' => $user['id']]);

            if (isset($staff)) {
                // yes
                // get staff_ids from staff table
                $staffs = R::getAll(
                    'SELECT client_id from clientstafs where staff_id=:staff_id',
                    [':staff_id' => $staff['id']]
                );

                // are they a supervisor?

                // yes
                // get staff_ids from staff table for each staff member they supervise
                // no
                // continue
            }

            // get any additional staff_ids from UserStaffAccess where allowed is 1
        }

        // list any staff_ids where allowed is 0 (Deny access to these staffs)
        $access_all_staffs = R::getAll('SELECT staff_id from userstaffs where user_id=:user_id and allowed=0', [':user_id' => $data['user_id']]);

        return ['http_code' => HTTP_OK, 'result' => $beans];
    }
}
