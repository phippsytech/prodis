<?php

namespace NDISmate\Services\ParticipantService;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;
use NDISmate\Utilities\ConvertFieldsToBoolean;

class ListParticipantsByUserId
{
    function __invoke($data, $fields, $guard)
    {
        $user_id = $guard->user_id;

        try {
            $beans = R::getAll(
                '(SELECT 
                        csa.client_id, 
                        clients.name as client_name,
                        clients.on_hold
                    FROM clientstaffassignments csa
                    JOIN clients 
                        ON clients.id = csa.client_id
                    JOIN staffs 
                        ON staffs.id = csa.staff_id
                    WHERE 
                        (clients.archived IS NULL OR clients.archived != 1)
                        AND staffs.user_id = :user_id
                )
                UNION
                (
                    SELECT 
                        s.client_id, 
                        clients.name as client_name,
                        clients.on_hold
                    FROM stakeholders s
                    JOIN clients 
                        ON clients.id = s.client_id
                    WHERE 
                        (clients.archived IS NULL OR clients.archived != 1)
                        AND s.user_id = :user_id
                )',
                [':user_id' => $user_id]
            );

            
            $beans = (new ConvertFieldsToBoolean)($beans, ['on_hold']);
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
