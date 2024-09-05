<?php
namespace NDISmate\Models\Staff;

use Respect\Validation\Validator as v;
use \NDISmate\CORE\CRUD;
use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class GetStaffer
{
    function __invoke($data)
    {
        $bean = R::getRow(
            'SELECT 
                users.display_name as name,
                users.first_name as first_name,
                users.last_name as last_name,
                users.email as email,
                users.phone as phone_number,
                staffs.id,
                staffs.user_id,
                staffs.billable_hours_kpi,
                staffs.ordinary_hours_rate,
                staffs.archived,
                staffs.xero_employee_ref,
                staffs.pin,
                staffs.level,
                staffs.xero_super_membership_ref,
                staffs.employment_type,
                staffs.employment_basis,
                staffs.paygrade_id,
                staffs.date_of_birth,
                staffs.address_line_1,
                staffs.suburb,
                staffs.state,
                staffs.postcode,
                staffs.address_line_1,
                staffs.groups,
                staffs.google_folder,
                staffs.staff_role,
                staffs.status
            FROM staffs 
            JOIN users on (staffs.user_id = users.id) 
            WHERE staffs.id=:staff_id
            ',
            [':staff_id' => $data['id']]
        );

        return ['http_code' => 200, 'result' => $bean];
    }
}
