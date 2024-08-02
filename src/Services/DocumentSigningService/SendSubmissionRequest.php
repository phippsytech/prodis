<?php
namespace NDISmate\Services\DocumentSigningService;

use NDISmate\CORE\ContentTemplate;
use NDISmate\Services\DocumentSigningService\DocuSealAPICall;

class SendSubmissionRequest
{
    public function __invoke($data)
    {
        try {
            $params = [
                'template_id' => $data['template_id'],
                'send_email' => false,  // to avoid actually sending requests.
                'submitters' => [
                    [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'external_id' => $data['external_id'],
                    ]
                ]
            ];

            $response = (new DocuSealAPICall)([
                'endpoint' => '/submissions',
                'data' => $params,
            ]);

            return $response;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
        }
    }
}
