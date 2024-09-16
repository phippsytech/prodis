<?php
namespace NDISmate\Models\Payroll\Payrun;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class ListKMsByStaffId
{
    function __invoke($data)
    {
        $sql = "SELECT 
            SUM(kms) AS kms
        FROM trips
        WHERE trip_date BETWEEN :start_date AND :end_date
        AND staff_id = :staff_id
        AND (vehicle_type='private' or vehicle_type is null)
        GROUP BY staff_id";

        $params = [':start_date' => $data['start_date'], ':end_date' => $data['end_date'], ':staff_id' => $data['staff_id']];

        $kms = R::getCell($sql, $params);

        if ($kms == 0)
            return [];

        // km allowance (below ATO rate)
        $kilometer_earnings_below_tax_rate_id = '5bf20862-2bb8-4e23-a1f9-f0f6e437d054';
        $earningsLines[] = [
            'earningsRateID' => $kilometer_earnings_below_tax_rate_id,
            'numberOfUnits' => $kms,
            'ratePerUnit' => '0.88',
        ];

        // km allowance (above ATO rate)
        $kilometer_earnings_above_tax_rate_id = '15ecfd02-4eb8-460a-aecd-c54b84941a2d';
        $earningsLines[] = [
            'earningsRateID' => $kilometer_earnings_above_tax_rate_id,
            'numberOfUnits' => $kms,
            'ratePerUnit' => '0.11',
        ];

        return $earningsLines;
    }
}
