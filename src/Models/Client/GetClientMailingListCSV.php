<?php
namespace NDISmate\Models\Client;

use \RedBeanPHP\R as R;

class GetClientMailingListCSV
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            'SELECT
                clients.name as ClientName,
                clients.date_of_birth as ClientDOB,
                clients.representative_email as ClientRepEmail,
                stakeholders.name as StakeholderName,
                stakeholders.email as StakeholderEmail,
                stakeholders.relationship as StakeholderRelationship,
                clients.archived as Archived
            FROM clients 
            JOIN stakeholders ON (clients.id = stakeholders.client_id)
            ORDER BY clients.name'
        );

        return $beans;

    }
}
