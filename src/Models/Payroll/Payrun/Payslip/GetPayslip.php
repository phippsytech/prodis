<?php
namespace NDISmate\Models\Payroll\Payrun\Payslip;

use \RedBeanPHP\R as R;

class GetPayslip
{
    function __invoke($xero, $data)
    {
        try {
            if (isset($data['staff_id'])) {
                $staff_id = $data['staff_id'];
            } else {
                $staff_id = null;
            }
            $payslip_id = $data['payslip_id'];

            // Get the payslip
            $apiResponse = $xero->payrollAuApi->getPayslip($xero->payroll_tenant_id, $payslip_id);

            $payslip = $apiResponse->getPayslip();

            $result = \XeroAPI\XeroPHP\PayrollAuObjectSerializer::sanitizeForSerialization($payslip);
            return $result;
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errors[] = ['message' => json_decode($e->getResponseBody(), true), 'staff_id' => $staff_id];
            return ['http_code' => 400, 'error_message' => $errors];
        }
    }
}
