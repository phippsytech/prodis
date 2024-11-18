<?php

namespace NDISmate\Services\ServiceAgreementService;

use RedBeanPHP\R as R;

class ListServiceAgreementsByStatus
{
    public function __invoke($data)
    {

        // if data is not one of the allowed values, throw an exception
        if (!in_array($data['status'], ['draft', 'pending', 'active', 'ended'])) {
            throw new \Exception('Invalid status value');
        }

        try {
            $sql =
                "SELECT serviceagreements.*, clients.name as client_name FROM serviceagreements
                JOIN clients ON serviceagreements.client_id = clients.id
                WHERE serviceagreements.status = ?
                ORDER BY id desc";

            // Execute the query and get the number of affected rows
            $beans = R::getAll($sql, [$data['status']]);


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
