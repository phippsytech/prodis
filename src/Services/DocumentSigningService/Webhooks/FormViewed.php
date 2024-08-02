<?php

namespace NDISmate\Services\DocumentSigningService\Webhooks;

use NDISmate\Models\SignatureRequest\SignatureRequestEvent;

class FormViewed
{
    public function __invoke($data)
    {
        $signature_request_event = (new SignatureRequestEvent)->create([
            'event' => 'viewed',
            'signature_request_id' => $data['external_id'],
        ]);
        return ['http_code' => 200];
    }
}
