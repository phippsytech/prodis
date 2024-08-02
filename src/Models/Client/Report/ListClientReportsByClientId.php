<?php
namespace NDISmate\Models\Client\Report;

use \RedBeanPHP\R as R;
use \RedBeanPHP\RedException;

class ListClientReportsByClientId
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll(
                'SELECT *
                FROM clientreports
                WHERE client_id = :client_id',
                [':client_id' => $data['client_id']]
            );

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
