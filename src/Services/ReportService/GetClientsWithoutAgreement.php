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
            LEFT JOIN serviceagreements ON clients.id = serviceagreements.client_id
            WHERE (serviceagreements.service_agreement_signed_date IS NULL OR serviceagreements.client_id IS NULL)
            AND (clients.archived != 1 OR clients.archived IS NULL)
            '
        );
        return $beans;
    }
}
