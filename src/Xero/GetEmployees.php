<?php
namespace NDISmate\Xero;

use \RedBeanPHP\R as R;

class GetEmployees
{
    function __invoke($xero)
    {
        $where = 'Status=="ACTIVE"';

        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroEmployees');

        if (empty($jsonData)) {  // Cache doesn't exist, fetch data
            try {
                $apiResponse = $xero->payrollAuApi->getEmployees($xero->payroll_tenant_id, null, $where);

                $jsonData = json_encode($apiResponse);

                $expiryInSeconds = 86400 ;
                // Write JSON data to Cache
                $cache = (new \NDISmate\Cache\Store)('xeroEmployees', $jsonData, $expiryInSeconds);
            } catch (\XeroAPI\XeroPHP\ApiException $e) {
                $error = json_decode($e->getResponseBody(), true);
                return ['http_code' => 400, 'error' => $error];
            }
        }

        $data = json_decode($jsonData, true);

        foreach ($data as $employee) {
            $result[] = [
                'id' => $employee['EmployeeID'],
                'name' => $employee['FirstName'] . ' ' . $employee['LastName'],
                'email' => $employee['Email'],
            ];
        }

        return ['http_code' => 200, 'result' => $result];
    }
}
