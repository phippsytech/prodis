<?php
namespace NDISmate\Models\Client\Report;

use NDISmate\Models\Client\Report\ListClientReports;
use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class ListClientReportsByStaffId
{
    public function __invoke($data)
    {
        try {
            $beans = (new ListClientReports)($data);

            $beans = array_filter($beans, function ($bean) use ($data) {
                return $bean['staff_id'] == $data['staff_id'] && !empty($bean['service_codes']);
                // return empty($bean['service_codes']);
                // return isset($bean['staff_id']) && $bean['staff_id'] == $data['staff_id'] && !empty($data['service_codes']);
            });

            // remove the key from $beans
            $beans = array_values($beans);

            return $beans;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
