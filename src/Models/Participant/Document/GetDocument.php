<?php

namespace NDISmate\Models\Participant\Document;

use \RedBeanPHP\R as R;


class GetDocument
{

    function __invoke($data)
    {

        $bean = R::getRow(
            'SELECT 
                id,
                details,
                vultr_storage_ref,
                document_date
            FROM participantdocuments 
            WHERE participant_id=:participant_id
                AND document_id=:document_id
            ',
            [
                ":participant_id" => $data['participant_id'],
                ":document_id" => $data["document_id"]
            ]
        );
        
        return $bean;
    }
}
