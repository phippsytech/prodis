<?php
namespace NDISmate\Models;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Staff extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('staff', [
            'id' => [v::numericVal()],
            'user_id' => [v::numericVal()],  // The staff record extends the user record
            // TODO: Need to remove this throughout staff records
            // 'level' => [v::numericVal()], // 1 - 4 used for staff earnings
            'paygrade_id' => [v::numericVal()],  // This replaces level!
            'groups' => [v::stringType()],  // an array of staff group names.
            'staff_role' => [v::stringType()],  // eg: therapist, admin, house
            'employment_type' => [v::stringType()],
            'name' => [v::stringType()],
            'first_name' => [v::stringType()],
            'last_name' => [v::stringType()],
            'phone_number' => [v::stringType()],
            'email' => [v::stringType()],  // this needs to be stored lowercase
            'date_of_birth' => [v::stringType()],
            'address_line_1' => [v::stringType()],
            'address_line_2' => [v::stringType()],
            'suburb' => [v::stringType()],
            'state' => [v::stringType()],
            'postcode' => [v::stringType()],
            'start_date' => [v::stringType()],
            'employment_basis' => [v::stringType()],
            'billable_hours_kpi' => [v::numericVal()],
            'hours_per_week' => [v::numericVal()],
            'ordinary_hours_rate' => [v::numericVal()],
            'status' => [v::stringType()],
            'archived' => [v::boolVal()],  // true
            'xero_employee_ref' => [v::stringType()],
            'xero_super_membership_ref' => [v::stringType()],
            'pin' => [v::stringType()],
            'google_folder' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addStaffer', 'addStaffer', true, ['admin']],
            ['listStaff', 'listStaff', true, []],
            ['listStaffByGroup', 'listStaffByGroup', true, []],
            ['searchStaff', 'searchStaff', true, []],
            ['listStaffByClientId', 'listStaffByClientId', true, []],
            ['getMyStaffId', 'getUserStaffId', true, []],
            ['getBillableHourTrendByStaffId', 'getBillableHourTrendByStaffId', false, []],
            ['getStaffKPI', 'getStaffKPI', true, []],
            ['getStafferKPI', 'getStafferKPI', true, []],
            ['getStaffer', 'getStaffer', true, []],
            ['updateStaffer', 'updateStaffer', true, []],
            ['bulkUpdateStaff', 'bulkUpdateStaff', true, ['payroll', 'admin']],
            ['archiveStaffer', 'archive', true, ['admin']],
            ['restoreStaffer', 'restore', true, ['admin']],
            ['updatePin', 'updatePin', true, ['house']],
            ['checkPin', 'checkPin', true, ['house']],
            ['createGoogleDriveFolders', 'createGoogleDriveFolders', false, ['admin']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function listStaffByGroup($data)
    {
        $group = $data['group'];
        $bean = R::getAll(
            "SELECT id, name
            FROM staffs
            WHERE JSON_CONTAINS(staffs.groups, :group, '\$')
            AND (archived != 1 or archived is null)",
            [':group' => "\"$group\""]
        );
        return ['http_code' => 200, 'result' => $bean];
    }

    function searchStaff($data)
    {
        $search = $data['search'];
        $bean = R::getAll(
            'SELECT id, name 
            FROM staffs 
            WHERE name LIKE :search 
            AND (archived != 1 or archived is null)',
            [':search' => "%$search%"]
        );
        return ['http_code' => 200, 'result' => $bean];
    }

    // bulk create google drive folders for participants in the connected directory
    function createGoogleDriveFolders($data)
    {
        return (new \NDISmate\Models\Staff\CreateGoogleDriveFolders)($data);
    }

    function addStaffer($data)
    {
        if (isset($data['groups'])) {
            $data['groups'] = json_encode($data['groups']);
        }
        $result = $this->create($data);
        $result = $result['result'];
        return ['http_code' => 201, 'result' => $result];
    }

    function getStaffer($data)
    {
        $result = (new \NDISmate\Models\Staff\GetStaffer)($data);
        // $result = $this->getOne($data);
        $result = $result['result'];
        $result['groups'] = json_decode($result['groups'], true);
        return ['http_code' => 200, 'result' => $result];
    }

    function bulkUpdateStaff($data)
    {
        $results = [];
        foreach ($data as $row) {
            $result = $this->update($row);
            $results[] = $result['result'];
        }

        return ['http_code' => 200, 'result' => $results];
    }

    function updateStaffer($data)
    {
        if (array_key_exists('groups', $data)) {
            $data['groups'] = json_encode($data['groups']);
        }
        $result = $this->update($data);
        $result = $result['result'];
        return ['http_code' => 200, 'result' => $data];
    }

    function listStaff($data)
    {
        $result = (new \NDISmate\Models\Staff\ListStaff)($data);
        $result = $result['result'];
        foreach ($result as &$row) {
            $row['groups'] = json_decode($row['groups'], true);
        }
        // $result['groups'] = json_decode($result['groups'], true);
        return ['http_code' => 200, 'result' => $result];
    }

    function listStaffByClientId($data)
    {
        return (new \NDISmate\Models\Staff\ListStaffByClientId)($data);
    }

    function updatePin($data)
    {
        $pin = $data['pin'];
        $staffId = $data['staff_id'];
        $staff = R::load('staffs', $staffId);
        $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
        $staff->pin = $hashedPin;
        R::store($staff);
        return ['http_code' => 200];
    }

    function checkPin($data)
    {
        $pin = $data['pin'];
        $staff_id = $data['staff_id'];
        $hashed_pin = R::getCell('SELECT pin FROM staffs WHERE id=:staff_id', [':staff_id' => $staff_id]);
        if (password_verify($pin, $hashed_pin)) {
            return ['http_code' => 200];
        } else {
            return ['http_code' => 403, 'error_message' => 'Incorrect pin.'];
        }
    }

    function getUserStaffId($data, $field, $guard)
    {
        $user_id = $guard->user_id;

        $bean = R::getRow(
            'SELECT 
                staffs.id,
                staffs.name,
                staffs.email
            FROM staffs 
            WHERE user_id=:user_id',
            [':user_id' => $user_id]
        );

        if (empty($bean))
            return ['http_code' => 404, 'error_message' => 'Not found.'];

        return ['http_code' => 200, 'result' => $bean];
    }

    function getStaffKPI($data)
    {
        $bean = R::getAll(
            "SELECT 
            staffs.id, 
            staffs.name, 
            services.code,
            staffs.billable_hours_kpi, 
            sum(session_duration) as total_session_minutes
        FROM timetrackings 
        JOIN staffs on (staffs.id=timetrackings.staff_id) 
        JOIN services on (services.id=timetrackings.service_id) 
            WHERE session_date>=:start_date AND session_date<=:end_date
            AND (timetrackings.unit_type<>'km' OR timetrackings.unit_type is null)
            AND JSON_CONTAINS(staffs.groups,'\"therapist\"','\$')
            GROUP BY staff_id, timetrackings.service_id
            ORDER BY staff_id",
            [
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date']
            ]
        );

        if (empty($bean))
            return ['http_code' => 404, 'error_message' => 'Not found.'];

        $staffArray = [];

        $staff_id = null;
        foreach ($bean as $row) {
            if ($staff_id != $row['id']) {
                $staff_id = $row['id'];
                $staffArray[$staff_id] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'billable_hours_kpi' => $row['billable_hours_kpi'],
                ];
            }

            $staffArray[$staff_id]['services'][] = [
                'code' => $row['code'],
                'total_session_minutes' => $row['total_session_minutes'],
                // 'total_travel_minutes' => $row['total_travel_minutes']
            ];

            $staffId = $row['id'];
        }

        return ['http_code' => 200, 'result' => array_values($staffArray)];
    }

    function getStafferKPI($data)
    {
        if (!isset($data['staff_id']))
            return ['http_code' => 400, 'error_message' => 'Missing staff_id.'];

        $bean = R::getAll(
            "SELECT 
            staffs.id, 
            staffs.name, 
            services.code,
            staffs.billable_hours_kpi, 
            sum(session_duration) as total_session_minutes
            
        FROM timetrackings 
        JOIN staffs on (staffs.id=timetrackings.staff_id) 
        JOIN services on (services.id=timetrackings.service_id) 
            WHERE staff_id=:staff_id AND session_date>=:start_date AND session_date<=:end_date
            AND (timetrackings.unit_type<>'km' OR timetrackings.unit_type is null)
            GROUP BY timetrackings.service_id",
            [
                ':staff_id' => $data['staff_id'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date']
            ]
        );

        if (empty($bean))
            return ['http_code' => 404, 'error_message' => 'Not found.'];

        $staffArray = [];

        $staff_id = null;
        foreach ($bean as $row) {
            if ($staff_id != $row['id']) {
                $staff_id = $row['id'];
                $staffArray[$staff_id] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'billable_hours_kpi' => $row['billable_hours_kpi'],
                ];
            }

            $staffArray[$staff_id]['services'][] = [
                'code' => $row['code'],
                'total_session_minutes' => $row['total_session_minutes'],
                // 'total_travel_minutes' => $row['total_travel_minutes']
            ];

            $staffId = $row['id'];
        }

        return ['http_code' => 200, 'result' => array_values($staffArray)];
    }

    function getBillableHourTrendByStaffId()
    {
        $bean = R::getAll(
            'SELECT 
                YEAR(session_date) as sy, 
                WEEK(session_date) as sw, 
                -- sum(session_duration) as duration 

                round(
                    sum(
                        session_duration
                        + IFNULL(CASE WHEN actual_travel_time > 30 THEN 30 ELSE actual_travel_time END,0)
                    ) / 60
                ,2)
                AS duration


            FROM timetrackings 
            WHERE staff_id=2
            GROUP BY sy, sw',
            [
                // ":start_date"=>$data['start_date'],
                // ":end_date"=>$data['end_date']
            ]
        );

        if (empty($bean))
            return ['http_code' => 404, 'error_message' => 'Not found.'];

        return ['http_code' => 200, 'result' => $bean];
    }
}
