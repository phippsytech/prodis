<?php
namespace NDISmate\Models\Payroll\Leave;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class LeaveRequest extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('leaverequest', [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'leave_type' => [v::stringType()],
            'is_unpaid' => [v::boolVal()],
            'reason' => [v::stringType()],
            'date_type' => [v::stringType()],
            'start_date' => [v::stringType()],
            'end_date' => [v::stringType()],
            'hours' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addLeaveRequest', 'addLeaveRequest', true, ['therapist', 'sil', 'admin']],
            ['getLeaveRequest', 'getOne', true, []],
            ['listLeaveRequests', 'listLeaveRequests', true, ['admin']],
            ['listLeaveRequestsByStaffId', 'listLeaveRequestsByStaffId', true, []],
            ['searchLeaveRequests', 'searchLeaveRequests', false, []],  // true, ["payroll","admin"]
            ['updateLeaveRequest', 'update', true, ['therapist', 'sil', 'admin']],  // Edit is easier than delete/add again
            ['deleteLeaveRequest', 'delete', true, ['therapist', 'sil', 'admin']],
            ['approveLeaveRequest', 'approveLeaveRequest', true, ['admin']],
            ['rejectLeaveRequest', 'rejectLeaveRequest', true, ['admin']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    function addLeaveRequest($data)
    {
        // massage the data based on date_type
        switch ($data['date_type']) {
            case 'range':
                // $days = $this->calculateDays($data['start_date'], $data['end_date']);
                // // $data['days']= $days;
                // $data['hours']=$days*7.6;
                break;

            case 'day':
                unset($data['end_date']);
                // $data['hours']=7.6;
                break;

            case 'part_day':
                unset($data['end_date']);
                break;
        }

        return $this->CRUD->create($data);
    }

    function listLeaveRequests($data)
    {
        $leaveRequests = R::getAll('SELECT leaverequests.*, staffs.name as staff_name from leaverequests join staffs on staffs.id=leaverequests.staff_id');
        return ['http_code' => 200, 'result' => $leaveRequests];
    }

    function listLeaveRequestsByStaffId($data)
    {
        $query = 'SELECT * from leaverequests WHERE staff_id=:staff_id';
        $params = [':staff_id' => $data['staff_id']];
        $leaveRequests = R::getAll($query, $params);
        return ['http_code' => 200, 'result' => $leaveRequests];
    }

    function searchLeaveRequests($data)
    {
        $leaveRequests = (new \NDISmate\Models\Payroll\Leave\LeaveRequest\SearchLeaveRequests)($data);
        return ['http_code' => 200, 'result' => $leaveRequests];
    }

    function calculateDays($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        // Add one day to the end date to make it inclusive
        $end->modify('+1 day');
        $interval = $start->diff($end);
        return $interval->days;
    }
}
