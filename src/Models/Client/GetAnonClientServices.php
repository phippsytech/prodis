<?php
namespace NDISmate\Models\Client;

use \RedBeanPHP\R as R;

class GetAnonClientServices
{
    public function __invoke($data)
    {
        $bean = R::getAll(
            'SELECT clients.id, md5(concat(clients.id,"phippsy")) anon_hash, clients.name, GROUP_CONCAT(services.code) AS service_codes
            FROM clients
            JOIN clientplans ON clients.id = clientplans.client_id
            JOIN clientplanservices ON clientplans.id = clientplanservices.plan_id
            JOIN services ON services.id = clientplanservices.service_id
            WHERE (clients.archived IS NULL OR archived != 1)
            GROUP BY clients.id
            ORDER BY clients.name
            '
        );

        if ($data['download'] == true) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="anonclientservices.csv"');

            $fp = fopen('php://memory', 'w');

            // headers
            fputcsv($fp, ['ClientID', 'ClientAnonHash', 'ClientName', 'ServiceCodes']);

            foreach ($bean as $fields) {
                fputcsv($fp, $fields);
            }

            rewind($fp);
            $csv = stream_get_contents($fp);

            fclose($fp);
            readfile($csv);
            print ($csv);
            return ['http_code' => 200];
        }

        return ['http_code' => 200, 'result' => $bean];
    }
}
