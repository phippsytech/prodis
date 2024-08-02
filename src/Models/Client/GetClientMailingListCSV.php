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

        return ['http_code' => 200, 'result' => $beans];

        //     header('Content-Type: text/csv');
        //     header('Content-Disposition: attachment; filename="anonclientservices.csv"');

        //     $fp = fopen('php://memory', 'w');

        //     // headers
        //     fputcsv($fp, ['ClientName', 'ClientDOB', 'ClientRepEmail', 'Archived', 'StakeholderName', 'StakeholderEmail', 'StakeholderRelationship']);

        //     foreach ($bean as $fields) {
        //         fputcsv($fp, $fields);
        //     }

        //     rewind($fp);
        //     $csv = stream_get_contents($fp);

        //     fclose($fp);
        //     readfile($csv);
        //     print ($csv);
        //     return ['http_code' => 200];
    }
}
