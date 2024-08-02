<?php
namespace NDISmate\Models\Payroll\Payrun;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class ListPPTByStaffId
{
    function __invoke($data, $employee, $payslip_data = [])
    {
        $staffer = R::getRow('SELECT 
            ordinary_hours_rate,
            hours_per_week, 
            employment_basis,
            paygrade_id 
            FROM staffs 
            WHERE employment_basis in ("SALARY", "FULLTIME", "PARTTIME")
            AND id=:staff_id', [
            ':staff_id' => $data['staff_id']
        ]);

        // if empty or null skip this staff member
        if (empty($staffer))
            return [];

        // need to adjust for any leave taken.  ()

        $earnings_rate_id = $employee->getOrdinaryEarningsRateId();

        $earnings_item = (new \NDISmate\Models\Payroll\GetEarningsItems)([
            'earnings_rate_id' => $earnings_rate_id
        ]);

        if (!$earnings_item['RatePerUnit'])
            $earnings_item['RatePerUnit'] = $staffer['ordinary_hours_rate'];

        if ($staffer['employment_basis'] != 'SALARY') {
            $hours_per_fortnight = $staffer['hours_per_week'] * 2;

            $hours_diff = $hours_per_fortnight - $this->getHoursFromEarnings($payslip_data['shifts'])
                - $this->getHoursFromAdjustments($payslip_data['adjustments']) - $payslip_data['leave'];

            if ($hours_diff > 0) {
                // If there are hours left to fill.

                $earningsLine = [
                    'earningsRateID' => $earnings_item['EarningsRateID'],
                    'numberOfUnits' => $hours_diff,
                    'ratePerUnit' => $earnings_item['RatePerUnit'],
                ];

                return [$earningsLine];
            } else {
                return [];  // no hours to fill.
            }
        }

        $earningsLine = [
            'earningsRateID' => $earnings_rate_id,
            'numberOfUnits' => ($staffer['hours_per_week'] * 2) - $payslip_data['leave'],  // this is fortnightly  - need to do this better I think.
            'ratePerUnit' => $earnings_item['RatePerUnit'],
        ];

        return [$earningsLine];
    }

    function getHoursFromEarnings($earningsLines)
    {
        $hours = 0;
        foreach ($earningsLines as $earningsLine) {
            // don't count passives as hours.
            if (in_array($earningsLine['earningsRateID'], [
                'ea122bfb-b265-4510-b3cb-0a868a55833c',  // $100
                '5e7e73ae-817d-4fdd-9a28-7a8a4d6c0f50',  // $130
                '074f499b-36ed-4508-baab-9c2a77f02fe6',  // $140
                '7f9f2716-1e09-4e8c-a855-7c14ed8b3348',  // 160
                'df392b54-5586-43ab-ab1a-5535b6bf3b93',  // BONUS
                // '6ff0152a-f0de-4a5f-a575-74cd08b20e6c',  // Bonus referral
                // "391b9cbd-0bad-4445-9b57-9181d50aa2b7", // Signing Bonus
                // "d0f26a9f-b1d3-4a94-9615-57c0a9ac98ea", // Bonus Overflow
            ]))
                continue;

            $hours += $earningsLine['numberOfUnits'];
        }

        return $hours;
    }

    function getHoursFromAdjustments($earningsLines)
    {
        $hours = 0;
        foreach ($earningsLines as $earningsLine) {
            // only count earnigs
            if (isset($earningsLine['earningsRateID']) && isset($earningsLine['numberOfUnits'])) {
                // don't count passives as hours.
                if (in_array($earningsLine['earningsRateID'], [
                    'ea122bfb-b265-4510-b3cb-0a868a55833c',  // $100
                    '5e7e73ae-817d-4fdd-9a28-7a8a4d6c0f50',  // $130
                    '074f499b-36ed-4508-baab-9c2a77f02fe6',  // $140
                    '7f9f2716-1e09-4e8c-a855-7c14ed8b3348',  // 160
                    'df392b54-5586-43ab-ab1a-5535b6bf3b93',  // BONUS
                    // '6ff0152a-f0de-4a5f-a575-74cd08b20e6c',  // Bonus referral
                    // "391b9cbd-0bad-4445-9b57-9181d50aa2b7", // Signing Bonus
                    // "d0f26a9f-b1d3-4a94-9615-57c0a9ac98ea", // Bonus Overflow
                ]))
                    continue;

                $hours += $earningsLine['numberOfUnits'];
            }
        }

        return $hours;
    }
}
