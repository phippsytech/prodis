<?php

namespace NDISmate\Models\Participant\Document;

use \RedBeanPHP\R as R;


class ListDueDocuments
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
            WHERE participantdocuments.expired_at BETWEEN CURDATE() 
                  AND CURDATE() + INTERVAL 1 MONTH
            ORDER BY clients.name'
        );
        
        
 

        return $beans;
    }
}
