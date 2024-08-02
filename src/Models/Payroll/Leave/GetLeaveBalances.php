<?php
namespace NDISmate\Models\Payroll\Leave;

use \RedBeanPHP\R as R;

class GetLeaveBalances
{
    function __invoke($xero, $data)
    {
        // Check Cache
        $staff_id = $data['staff_id'];

        $expiryDateTime = new \DateTime();
        $expiryDateTime->setTime(0, 0, 0);

        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroLeaveBalances' . $staff_id, $expiryDateTime);

        if (empty($jsonData)) {  // Cache doesn't exist, fetch data

            $staffer = R::getRow('SELECT xero_employee_ref, paygrade_id FROM staffs WHERE id=:staff_id', [':staff_id' => $staff_id]);

            // if xero_employee_ref is empty or null skip this staff member
            if (empty($staffer['xero_employee_ref']))
                return ['http_code' => 400, 'error' => 'No Xero Employee Ref'];

            $employee_id = $staffer['xero_employee_ref'];

            $apiResponse = $xero->payrollAuApi->getEmployee($xero->payroll_tenant_id, $employee_id);

            $xero_leave_balances = $apiResponse[0]->getLeaveBalances();

            foreach ($xero_leave_balances as $leave_balance) {
                $leave_balances[] = [
                    'leave_name' => $leave_balance->getLeaveName(),
                    'leave_type_id' => $leave_balance->getLeaveTypeID(),
                    'number_of_units' => $leave_balance->getNumberOfUnits(),
                    'type_of_units' => $leave_balance->getTypeOfUnits(),
                ];
            }

            $jsonData = json_encode($leave_balances);

            $cache = (new \NDISmate\Cache\Store)('xeroLeaveBalances' . $staff_id, $jsonData);
        } else {
            $leave_balances = json_decode($jsonData, true);
        }

        return ['http_code' => 200, 'result' => $leave_balances];
    }
}
