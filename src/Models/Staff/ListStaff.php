<?php
namespace NDISmate\Models\Staff;

use Respect\Validation\Validator as v;
use \NDISmate\CORE\CRUD;
use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class ListStaff
{
    function __invoke($data)
    {
        $bean = R::getAll(
            'SELECT 
                staffs.id,
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
                users.phone
            FROM staffs 
            JOIN users on (staffs.user_id = users.id) 
        '
        );

        return ['http_code' => 200, 'result' => $bean];
    }
}
