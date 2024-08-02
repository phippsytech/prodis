<?php
namespace NDISmate\Models\Payroll;

/**
 * Get the Pay Fortnight that a date falls in
 */
class GetPayFortnight
{
    function __invoke($date)
    {
        // Hardcoded start date of a pay fortnight (YYYY-MM-DD)
        $startDate = '2023-11-13';  // This should be a setting

        // Convert both dates to DateTime objects
        $start = new \DateTime($startDate);
        $current = new \DateTime($date);

        // Calculate the difference in weeks
        $weeks = $start->diff($current)->days / 7;

        // Determine the current fortnight (round down to the nearest even number)
        $currentFortnight = floor($weeks / 2) * 2;

        // Calculate start date of the current fortnight
        $fortnightStart = clone $start;
        $fortnightStart->modify("+{$currentFortnight} weeks");

        // Calculate end date of the current fortnight
        $fortnightEnd = clone $fortnightStart;
        $fortnightEnd->modify('+1 week 6 days');

        return [
            'start_date' => $fortnightStart->format('Y-m-d'),
            'end_date' => $fortnightEnd->format('Y-m-d')
        ];
    }
}
