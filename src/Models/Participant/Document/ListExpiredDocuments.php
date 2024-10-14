<?php

namespace NDISmate\Models\Participant\Document;

use \RedBeanPHP\R as R;


class ListExpiredDocuments
{

    function __invoke($data)
    {

        $beans = R::getAll(

            'SELECT 
                participantdocuments.id as id,
                participants.id as participant_id,
                participants.name as name, 
                participantdocuments.document_date,
                documents.name as document, 
                documents.date_collection_option,
                documents.years_until_expiry
            FROM participantdocuments
            JOIN documents ON (participantdocuments.document_id = documents.id)
            JOIN clients ON (clients.id = participantdocuments.participant_id)
            ORDER BY clients.name
            '
        );

        $expired = [];

        foreach ($beans as $key => $bean) {

            // if there isn't a date collection option, then we don't need to worry about expiry
            if (!in_array($bean['date_collection_option'], ["issued", "expires"])) continue;

            if ($bean['date_collection_option'] == "issued") {
                if (!$bean['years_until_expiry']) continue;
                $expiry_date = date('Y-m-d', strtotime($bean['document_date'] . ' + ' . $bean['years_until_expiry'] . ' years'));
            }

            if ($bean['date_collection_option'] == "expires") {
                $expiry_date = $bean['document_date'];
            }

            // if the expiry date is in the past, then we need to flag it as expired
            if (strtotime($expiry_date) < strtotime(date('Y-m-d'))) {

                $expired[] = [
                    "participant_id" => $bean['participant_id'],
                    "document_id" => $bean['id'],
                    "name" => $bean['name'],
                    "document" => $bean['document'],
                    "expiry_date" => $expiry_date
                ];
            }
        }

        return $expired;
    }
}
