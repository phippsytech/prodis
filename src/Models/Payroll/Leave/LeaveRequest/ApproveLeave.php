<?php
namespace NDISmate\Models\Payroll\Leave\LeaveRequest;

use \RedBeanPHP\R as R;

class ApproveLeave
{
    // https://xeroapi.github.io/xero-php-oauth2/docs/v2/payroll_au/index.html#api-PayrollAu-createLeaveApplication
    // https://developer.xero.com/documentation/api/payrollau/leaveapplications

    function __invoke($xero)
    {
        $startDate = new DateTime('2020-10-28');
        $endDate = new DateTime('2020-10-30');

        $leaveApplication = new XeroAPI\XeroPHP\Models\PayrollAu\LeaveApplication;
        $leaveApplication->setEmployeeId('00000000-0000-0000-0000-000000000000');
        $leaveApplication->setLeaveTypeId('00000000-0000-0000-0000-000000000000');
        $leaveApplication->setEmployeeId();
        $leaveApplication->setLeaveTypeId();
        $leaveApplication->setTitle('Annual Leave');
        $leaveApplication->setStartDate($startDate);
        $leaveApplication->setEndDate($endDate);
        // $leaveApplication->setDescription();
        // $leaveApplication->setPayOutType();
        // $leaveApplication->setLeavePeriods();

        try {
            $apiResponse = $xero->payrollAuApi->createLeaveApplication(
                xero_tenant_id: $xero->payroll_tenant_id,
                leave_application: $leaveApplication,
                // idempotency_key: null
            );
            return ['http_code' => 200, 'result' => $apiResponse];
        } catch (\Exception $e) {
            print_r($e);
            $error = $e->getMessage();
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
