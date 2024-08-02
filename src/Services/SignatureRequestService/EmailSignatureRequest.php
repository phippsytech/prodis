<?php
namespace NDISmate\Services\SignatureRequestService;

use NDISmate\CORE\ContentTemplate;
use NDISmate\Models\SignatureRequest\SignatureRequestEvent;
use NDISmate\Models\SignatureRequest;
use NDISmate\Services\EmailService;

class EmailSignatureRequest
{
    public function __invoke($data)
    {
        try {
            // load document signing request
            $signature_request = (new SignatureRequest)->load($data['signature_request_id']);

            // record that the document has been sent.
            $signature_request_event = (new SignatureRequestEvent)->create([
                'event' => 'sent',
                'signature_request_id' => $data['signature_request_id'],
            ]);

            $email = $signature_request->email;
            // $email = 'phippsy@phippsy.tech';  // hardcoded email for testing

            $response = EmailService::send([
                'to_email' => $email,
                'from_name' => 'Crystel Care',
                'from_email' => 'crystel@crystelcare.com.au',
                'subject' => 'Please Sign: Updated Service Agreement for ' . $signature_request->participant_name,
                'html' => (new ContentTemplate)->render([
                    'template' => 'signing-link.html',
                    'template_variables' => [
                        'ACTION_URL' => $signature_request->url,
                        'ORGANISATION_NAME' => 'Crystel Care',
                        'ORGANISATION_ADDRESS' => '',
                        'PARTICIPANT_NAME' => $signature_request->participant_name,
                        'SUPPORT_URL' => 'https://crystelcare.com.au',
                        'EMAIL_ADDRESS' => $email,
                    ],
                ])
            ]);

            return $response;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
        }
    }
}
