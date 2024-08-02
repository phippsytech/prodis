<?php

namespace NDISmate\Services\DocumentSigningService\Webhooks;

use NDISmate\Models\SignatureRequest\SignatureRequestEvent;
use NDISmate\Models\SignatureRequest;

class FormCompleted
{
    public function __invoke($data)
    {
        $signature_request = (new SignatureRequest)->update([
            'id' => $data['external_id'],
            // 'signed_document_url' => $data['documents'][0]['url']
            'signed_document_url' => $data['submission']['url']
        ]);

        $signature_request_event = (new SignatureRequestEvent)->create([
            'event' => 'signed',
            'signature_request_id' => $data['external_id'],
        ]);
        return ['http_code' => 200];
    }
}
