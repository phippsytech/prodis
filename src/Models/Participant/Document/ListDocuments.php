<?php

namespace NDISmate\Models\Participant\Document;

use \RedBeanPHP\R as R;

class ListDocuments
{

    function __invoke($data)
    {

        $beans = R::getAll(

            'SELECT 
                participantdocuments.id as id,
                participants.id as participant_id,
                participants.name as name, 
                participants.groups as participant_groups,
                participantdocuments.document_date,
                documents.name as document, 
                documents.collect_from_therapist,
                documents.collect_from_sil,
                documents.date_collection_option,
                documents.years_until_expiry
            FROM participantdocuments
            JOIN documents ON (participantdocuments.document_id = documents.id)
            JOIN participants ON (participants.id = participantdocuments.participant_id)
            '
        );

        foreach ($beans as $key => $bean) {
            $beans[$key]['participant_groups'] = json_decode($bean['participant_groups']);
        }

        return $beans;
    }
}
