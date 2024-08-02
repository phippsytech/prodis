<?php
namespace NDISmate\Models\SignatureRequest;

use RedBeanPHP\R as R;

class ListSignatureRequestEvents
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            'SELECT
                id,
                event,
                created
            FROM
                signaturerequestevents
            WHERE
                signature_request_id=:signature_request_id
            ORDER BY
                created DESC
            ', [':signature_request_id' => $data['signature_request_id']]
        );
        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }
    }
}
