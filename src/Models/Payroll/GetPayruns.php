<?php
namespace NDISmate\Models\Payroll;

use \RedBeanPHP\R as R;

class GetPayruns
{
    function __invoke($xero)
    {
        $ifModifiedSince = new \DateTime('2023-10-30T12:17:43.202-08:00');
        // $where = "Status=="ACTIVE"";
        // $order = "EmailAddress%20DESC";
        // $page = 56;

        try {
            //   $result = $xero->payrollAuApi->->getPayRuns($xero->payroll_tenant_id,, $ifModifiedSince, $where, $order, $page);

            $result = $xero->payrollAuApi->getPayRuns($xero->payroll_tenant_id, $ifModifiedSince);
            return ['http_code' => 200, 'result' => $result];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            // print_r($e);
            if ($e->getCode() == 429) {  // Rate limit error
                // Retrieve the Retry-After header value (in seconds)
                $retryAfter = $e->getResponseHeaders()['Retry-After'][0] ?? null;
                $appMinLimitRemaining = $e->getResponseHeaders()['X-AppMinLimit-Remaining'][0] ?? null;
                $minLimitRemaining = $e->getResponseHeaders()['X-MinLimit-Remaining'][0] ?? null;
                $dayLimitRemaining = $e->getResponseHeaders()['X-DayLimit-Remaining'][0] ?? null;
                $rateLimitProblem = $e->getResponseHeaders()['X-Rate-Limit-Problem'][0] ?? null;

                if (!is_null($retryAfter)) {
                    // Convert Retry-After to an integer if not already
                    $retryAfter = (int) $retryAfter;
                    // Log this information or handle it according to your application's needs
                    $error = [
                        'retryAfter' => $retryAfter,
                        'appMinLimitRemaining' => $appMinLimitRemaining,
                        'minLimitRemaining' => $minLimitRemaining,
                        'dayLimitRemaining' => $dayLimitRemaining,
                        'rateLimitProblem' => $rateLimitProblem
                    ];
                    // Implement your retry mechanism here (e.g., sleep for $retryAfter seconds, then retry)
                } else {
                    // Handle cases where Retry-After is not provided
                    $error = "Rate limit exceeded, but no Retry-After header provided.\n";
                }
            } else {
                // Handle other types of exceptions

                // $error = json_decode($e->getResponseBody(), true);
                $error = json_decode($e->getMessage(), true);
            }

            // $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
