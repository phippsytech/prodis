<?php

namespace NDISmate\Models\Participant\Document;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class ListParticipantByDocumentId
{

    function __invoke($data)
    {
        
        $bean = R::getAll(
            'SELECT 
                *
            FROM participantdocuments 
            WHERE document_id=:document_id
            ',
            [
                ":document_id" => $data["document_id"]

            ]
        );

        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];

        return ["http_code" => 200, "result" => $bean];
        // return ["http_code"=>200, "result"=>$bean->export()];

    }
}
