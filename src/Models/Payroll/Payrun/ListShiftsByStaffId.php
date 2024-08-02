<?php
namespace NDISmate\Models\Payroll\Payrun;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class ListShiftsByStaffId
{
    function __invoke($data)
    {
        $staff_id = $data['staff_id'];

        $earnings = (new \NDISmate\Models\Payroll\Payrun\GetEarnings)($data);

        // print_r($earnings);

        $earnings = $this->reformatEarningsData($earnings);

        // print_r($earnings);

        $staffer = R::getRow('SELECT xero_employee_ref, paygrade_id, employment_basis FROM staffs WHERE id=:staff_id', [':staff_id' => $staff_id]);

        // if xero_employee_ref is empty or null skip this staff member
        if (empty($staffer['xero_employee_ref']))
            return [];

        $paygrade_id = $staffer['paygrade_id'];

        $earningsLines = [];

        foreach ($earnings as $shift_type => $numberOfUnits) {
            // if shift type is not SLEEP and employment_basis is SALARY skip this shift
            if ($shift_type != 'SLEEP' && $staffer['employment_basis'] == 'SALARY')
                continue;

            $earnings_rate_id = $this->getEarningsRateId($paygrade_id, $shift_type);
            $earnings_item = (new \NDISmate\Models\Payroll\GetEarningsItems)(['earnings_rate_id' => $earnings_rate_id]);

            $earningsLine = [
                'earningsRateID' => $earnings_rate_id,
                'numberOfUnits' => $numberOfUnits,
                'ratePerUnit' => $earnings_item['RatePerUnit'],
            ];

            $earningsLines[] = $earningsLine;
        }

        return $earningsLines;
    }

    function getEarningsRateId($paygrade_id, $shift_type)
    {
        $xeropaygrade = R::getRow('SELECT sleep_over, public_holiday, sunday, saturday, night, evening, `day` FROM xeropaygrades WHERE id=:paygrade_id', [':paygrade_id' => $paygrade_id]);

        switch ($shift_type) {
            case 'SLEEP':
                return $xeropaygrade['sleep_over'];
            case 'PUB':
                return $xeropaygrade['public_holiday'];
            case 'SUN':
                return $xeropaygrade['sunday'];
            case 'SAT':
                return $xeropaygrade['saturday'];
            case 'NIGHT':
                return $xeropaygrade['night'];
            case 'EVE':
                return $xeropaygrade['evening'];
            case 'DAY':
                return $xeropaygrade['day'];
        }
    }

    function reformatEarningsData($earnings)
    {
        $result = [];

        foreach ($earnings as $row) {
            $staffId = $row['staff_id'];
            $shift_type = $row['shift_type'];

            $duration = $row['duration'] / 60;

            // Passive duration is always 1 hour
            if ($shift_type == 'SLEEP') {
                $duration = 1;
            }

            if (!isset($result[$shift_type])) {
                $result[$shift_type] = 0;
            }
            $result[$shift_type] += $duration;
        }

        return $result;
    }
}
