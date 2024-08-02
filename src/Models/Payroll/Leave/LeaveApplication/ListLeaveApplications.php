<?php
namespace NDISmate\Models\Payroll\Leave\LeaveApplication;

use \RedBeanPHP\R as R;

class ListLeaveApplications
{
    function __invoke($xero, $data)
    {
        try {
            $result = $xero->payrollAuApi->getLeaveApplications($xero->payroll_tenant_id);

            $leaveApplications = $result->getLeaveApplications();

            $leave_application = [];
            $leave_applications = [];

            foreach ($leaveApplications as $leaveApplication) {
                $leave_application['start_date'] = $this->convertXeroDate($leaveApplication['start_date']);
                $leave_application['end_date'] = $this->convertXeroDate($leaveApplication['end_date']);
                $leave_application['leave_application_id'] = $leaveApplication['leave_application_id'];
                $leave_application['employee_id'] = $leaveApplication['employee_id'];
                $leave_application['staffer'] = $this->getStaffByXeroEmployeeId($leaveApplication['employee_id']);
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

    function getStaffByXeroEmployeeId($xero_employee_id)
    {
        $staff = R::getRow('SELECT id, name, first_name, last_name FROM staffs WHERE xero_employee_ref=:xero_employee_ref', [
            ':xero_employee_ref' => $xero_employee_id
        ]);

        return $staff;
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
