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
                clients.id as participant_id,
                clients.name as name, 
                participantdocuments.document_date,
                documents.name as document, 
                documents.years_until_expiry,
                participantdocuments.expired_at
            FROM participantdocuments
            JOIN documents ON (participantdocuments.document_id = documents.id)
            JOIN clients ON (clients.id = participantdocuments.participant_id)
            WHERE participantdocuments.expired_at < now()
            ORDER BY clients.name
            '
        );

    

        return $beans;
    }
}
