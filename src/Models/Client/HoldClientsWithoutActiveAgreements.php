<?php
namespace NDISmate\Models\Client;

use RedBeanPHP\R;

class HoldClientsWithoutActiveAgreements
{
    public function __invoke(): array
    {
        $this->holdClientsWithoutActiveAgreements();
        return [];
    }

    private function holdClientsWithoutActiveAgreements(): void
    {
        $query = '
            UPDATE clients c
            JOIN (
                SELECT c.id
                FROM clients c
                LEFT JOIN serviceagreements cp
                ON c.id = cp.client_id AND cp.is_active = 1
                GROUP BY c.id
                HAVING COUNT(cp.id) = 0
            ) AS subquery ON c.id = subquery.id
            SET c.on_hold = 1
            WHERE c.on_hold <> 1;
        ';

        R::exec($query);
    }
}



