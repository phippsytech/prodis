<?php

namespace NDISmate\Models\Participant\Document;

use \RedBeanPHP\R as R;
use NDISmate\Utilities\ConvertFieldsToBoolean;

class ListDocumentsByParticipantId
{

    function __invoke($data)
    {

        // if participant_id exists, use it or throw an exception
        $participant_id = $data['participant_id'] ??  throw new Exception('Neither participant_id nor client_id exists in $data.');




        $query = 
            "SELECT
                documenttypes.id,
                documenttypes.name,
                documenttypes.description,
                documenttypes.is_required
            FROM documenttypes
            LEFT JOIN participantdocuments 
                ON documenttypes.id = participantdocuments.documenttype_id 
                AND participantdocuments.participant_id = :participant_id
            ORDER BY documenttypes.is_required DESC, documenttypes.name";

        $params = [
            ":participant_id" => $participant_id,
        ];


        $beans = R::getAll($query, $params);

        $beans = (new ConvertFieldsToBoolean)($beans, ['is_required']);

        return $beans;
    }
}
