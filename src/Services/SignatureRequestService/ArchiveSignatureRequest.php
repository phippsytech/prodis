<?php
namespace NDISmate\Services\SignatureRequestService;

use NDISmate\CORE\ContentTemplate;
use NDISmate\Models\SignatureRequest\SignatureRequestEvent;
use NDISmate\Models\SignatureRequest;
use NDISmate\Services\DocumentSigningService\DocuSealAPI;

class ArchiveSignatureRequest
{
    public function __invoke($data)
    {
        try {
            // load document signing request
            $signature_request = (new SignatureRequest)->load($data['signature_request_id']);

            $result = DocuSealAPI::call([
                'endpoint' => '/templates/' . $signature_request->template_id,
                'method' => 'DELETE',
                'data' => [
                    'id' => $signature_request->template_id,
                ],
            ]);

            // record that the document has been sent.
            $signature_request_event = (new SignatureRequestEvent)->create([
                'event' => 'archived',
                'signature_request_id' => $signature_request->id,
            ]);

            return $response;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
        }
    }
}
