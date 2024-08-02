<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class GetClientsWithoutAgreement
{
    public function __invoke($data)
    {
        // TODO: This query will need to be improved more when we introduce the
        // ability for a participant to have multiple service agreements

        $beans = R::getAll(
            'SELECT 
                clients.id,
                clients.name,
                clients.on_hold
            FROM clients
            LEFT JOIN clientplans ON clients.id = clientplans.client_id
            WHERE (clientplans.service_agreement_signed_date IS NULL OR clientplans.client_id IS NULL)
            AND (clients.archived != 1 OR clients.archived IS NULL)
            '
        );
        return $beans;
    }
}
