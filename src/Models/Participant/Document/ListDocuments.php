<?php

namespace NDISmate\Models\Participant\Document;

use \RedBeanPHP\R as R;

class ListCredentials
{

    function __invoke($data)
    {

        $beans = R::getAll(

            'SELECT 
                clients.id as id,
                clients.id as staff_id,
                clients.name as name, 
                clients.groups as participant_groups,
                participantdocuments.document_date,
                documents.name as credential, 
                documents.collect_from_therapist,
                documents.collect_from_sil,
                documents.date_collection_option,
                documents.years_until_expiry
            FROM participantdocuments
            JOIN documents ON (participantdocuments.document_id = documents.id)
            JOIN clients ON (staffs.id = participantdocuments.participant_id)
            '
        );

        foreach ($beans as $key => $bean) {
            $beans[$key]['participant_groups'] = json_decode($bean['participant_groups']);
        }

        return $beans;
    }
}
