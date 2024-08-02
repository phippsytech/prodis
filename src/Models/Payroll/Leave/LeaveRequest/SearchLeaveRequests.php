<?php
namespace NDISmate\Models\Payroll\Leave\LeaveRequest;

use \RedBeanPHP\R as R;

class SearchLeaveRequests
{
    function __invoke($data)
    {
        // $fortnight = (new \NDISmate\Models\Payroll\GetPayFortnight)($start_date);

        $sql = 'SELECT *
            FROM leaverequests
            WHERE (start_date BETWEEN :start_date AND :end_date
            OR end_date BETWEEN :start_date AND :end_date)
            AND staff_id = :staff_id';

        $params = [
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':staff_id' => $data['staff_id']
        ];

        $leave_requests = R::getAll($sql, $params);

        return $leave_requests;
    }
}
