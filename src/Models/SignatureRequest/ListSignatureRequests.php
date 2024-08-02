<?php
namespace NDISmate\Models\SignatureRequest;

use RedBeanPHP\R as R;

class ListSignatureRequests
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            "SELECT
                sr.id,
                sr.title,
                sr.type,
                sr.representative_name,
                sr.participant_name,
                sr.email,
                sr.url,
                sr.signed_document_url,
                sre.event AS most_recent_event,
                sre.created AS event_created
            FROM
                signaturerequests sr
            LEFT JOIN signaturerequestevents sre
                ON sre.id = (
                    SELECT
                        s1.id
                    FROM
                        signaturerequestevents s1
                    WHERE
                        s1.signature_request_id = sr.id
                    ORDER BY
                        s1.created DESC
                    LIMIT 1
                )
            WHERE sre.event != 'archived'
            ORDER BY
                sre.created DESC
            "
        );
        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }
    }
}
