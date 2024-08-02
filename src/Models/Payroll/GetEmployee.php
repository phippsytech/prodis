<?php
namespace NDISmate\Models\Payroll;

use \RedBeanPHP\R as R;

class GetEmployee
{
    function __invoke($xero, $data)
    {
        $employee_id = $data['employee_id'];

        try {
            $apiResponse = $xero->payrollAuApi->getEmployee($xero->payroll_tenant_id, $employee_id);

            $employee = $apiResponse[0];

            $result = \XeroAPI\XeroPHP\PayrollAuObjectSerializer::sanitizeForSerialization($employee);

            // $employee_leave_balances = $apiResponse[0]->getLeaveBalances();

            // $leaveBalances=[];

            // foreach($employee_leave_balances as $value){

            //     $leaveBalances[]=[
            //         "leave_name"=>$value->getLeaveName(),
            //         "leave_type_id"=>$value->getLeaveTypeId(),
            //         "number_of_units"=>$value->getNumberOfUnits(),
            //         "type_of_units"=>$value->getTypeOfUnits()
            //     ];

            // }

            return ['http_code' => 200, 'result' => $result];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
