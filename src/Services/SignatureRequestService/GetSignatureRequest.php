<?php
namespace NDISmate\Services\SignatureRequestService;

use NDISmate\CORE\ContentTemplate;
use NDISmate\Models\SignatureRequest\SignatureRequestEvent;
use NDISmate\Models\SignatureRequest;
use NDISmate\Services\DocumentSigningService\DocuSealAPI;

class GetSignatureRequest
{
    public function __invoke($data)
    {
        try {
            // load document signing request
            $signature_request = (new SignatureRequest)->load($data['signature_request_id']);

            $result = DocuSealAPI::call([
                'endpoint' => '/templates/' . $signature_request->template_id,
                'method' => 'GET',
                'data' => [
                    'id' => $signature_request->template_id,
                ],
            ]);

            $document = $result['documents'][0];

            $response = $signature_request->export();
            $response['document'] = $document;
            return $response;

            // print_r($signature_request);

            return $signature_request;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
        }
    }
}
