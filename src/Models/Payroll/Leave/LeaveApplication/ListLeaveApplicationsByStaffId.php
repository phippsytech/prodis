<?php
namespace NDISmate\Models\Payroll\Leave\LeaveApplication;

use \RedBeanPHP\R as R;

class ListLeaveApplicationsByStaffId
{
    function __invoke($xero, $data)
    {
        $staff_id = $data['staff_id'];

        $staffer = R::getRow('SELECT xero_employee_ref, paygrade_id FROM staffs WHERE id=:staff_id', [':staff_id' => $staff_id]);

        // if xero_employee_ref is empty or null skip this staff member
        if (empty($staffer['xero_employee_ref']))
            return ['http_code' => 400, 'error' => 'No Xero Employee Ref'];

        $employee_id = $staffer['xero_employee_ref'];

        // EmployeeID == Guid(\"$employeeId\")
        $where = 'EmployeeID==Guid("' . $employee_id . '")';
        // $where = 'EmployeeID=="7c29ee7d-2c5d-456d-b1e8-399b14659e9f" AND Status=="SCHEDULED"';

        try {
            $result = $xero->payrollAuApi->getLeaveApplications($xero->payroll_tenant_id, null, $where);

            $leaveApplications = $result->getLeaveApplications();

            $leave_application = [];
            $leave_applications = [];

            foreach ($leaveApplications as $leaveApplication) {
                $leave_application['start_date'] = $this->convertXeroDate($leaveApplication['start_date']);
                $leave_application['end_date'] = $this->convertXeroDate($leaveApplication['end_date']);
                $leave_application['leave_application_id'] = $leaveApplication['leave_application_id'];
                $leave_application['employee_id'] = $leaveApplication['employee_id'];
                $leave_application['leave_type_id'] = $leaveApplication['leave_type_id'];
                $leave_application['title'] = $leaveApplication['title'];

                $leavePeriods = $leaveApplication->getLeavePeriods();
                $leave_period = [];
                $leave_periods = [];

                foreach ($leavePeriods as $leavePeriod) {
                    $leave_period['number_of_units'] = $leavePeriod['number_of_units'];
                    $leave_period['leave_period_status'] = $leavePeriod['leave_period_status'];
                    $leave_period['pay_period_end_date'] = $this->convertXeroDate($leavePeriod['pay_period_end_date']);
                    $leave_period['pay_period_start_date'] = $this->convertXeroDate($leavePeriod['pay_period_start_date']);
                    $leave_periods[] = $leave_period;
                }

                $leave_application['LeavePeriods'] = $leave_periods;
                $leave_applications[] = $leave_application;
            }

            return ['http_code' => 200, 'result' => $leave_applications];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errors[] = ['message' => json_decode($e->getResponseBody(), true), 'staff_id' => $staff_id];
            return ['http_code' => 400, 'error_message' => $errors];
        }
    }

    function convertXeroDate($xeroDate)
    {
        // Extract timestamp using regular expression
        preg_match('/\/Date\((\d+)([\+\-]\d+)?\)\//', $xeroDate, $matches);
        $timestamp = $matches[1] / 1000;  // Convert milliseconds to seconds

        // Create a DateTime object from the timestamp
        $date = new \DateTime();
        $date->setTimestamp($timestamp);

        // Return the formatted date
        return $date->format('Y-m-d');
    }
}
