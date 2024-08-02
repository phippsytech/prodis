<?php

namespace NDISmate\Services\ServiceAgreementService;

use NDISmate\CORE\ContentTemplate;
use NDISmate\Models\SignatureRequest\SignatureRequestEvent;
use NDISmate\Models\SignatureRequest;
use NDISmate\Services\DocumentSigningService\DocuSealAPI;
use NDISmate\Services\EmailService;

// This class is focused on creating a document, sending it to DocuSeal for signing, and sending an email to the participant's representative.

class GenerateServiceAgreementAmendment
{
    public function __invoke($data)
    {
        try {
            $result = DocuSealAPI::call([
                'endpoint' => '/templates/html',
                'data' => [
                    'html' => (new ContentTemplate)->render([
                        'template' => 'service-agreement-amendment.html',
                        'template_variables' => [
                            'PARTICIPANT_NAME' => $data['participant_name'],
                            'NDIS_NUMBER' => $data['ndis_number'],
                            'REPRESENTATIVE_NAME' => $data['representative_name'],
                            'REPRESENTATIVE_PHONE' => $data['representative_phone'],
                            'REPRESENTATIVE_EMAIL' => $data['representative_email'],
                            'SERVICE_AGREEMENT_START_DATE' => (new \DateTime($data['service_agreement_start_date']))->format('j M Y'),
                            'SERVICE_AGREEMENT_END_DATE' => (new \DateTime($data['service_agreement_end_date']))->format('j M Y'),
                            'AMENDMENT_START_DATE' => (new \DateTime($data['amendment_start_date']))->format('j M Y'),
                            'CURRENT_DATE' => (new \DateTime())->format('j M Y'),
                            'SERVICES' => $data['services'],
                        ],
                    ]),
                    'size' => 'A4',
                    'name' => $data['participant_name'] . ' - Service Agreement Amendment',
                    'folder_name' => 'Crystel Care',
                ],
            ]);

            print_r($result);

            $template_id = $result['id'];

            $signature_request = (new SignatureRequest)->create([
                'template_id' => $template_id,
                'title' => $data['participant_name'] . ' - Service Agreement Amendment',
                'type' => 'service_agreement_amendment',
                'representative_name' => $data['representative_name'],
                'participant_name' => $data['participant_name'],
                'email' => $data['representative_email'],
                'status' => 'pending',
                'related_id' => $data['service_agreement_id'],
                'action' => json_encode($this->createActionJSPA($data)),
            ]);

            $signature_request_event = (new SignatureRequestEvent)->create([
                'event' => 'pending',
                'signature_request_id' => $signature_request->id,
            ]);

            $result = DocuSealAPI::call([
                'endpoint' => '/submissions',
                'data' => [
                    'template_id' => $template_id,
                    'send_email' => false,  // to avoid actually sending requests.
                    'submitters' => [
                        [
                            'name' => $data['representative_name'],
                            'email' => $data['representative_email'],
                            'external_id' => $signature_request->id,
                        ]
                    ]
                ],
            ]);

            // This is the document signing link
            $embed_src = $result[0]['embed_src'];

            // Update the signing request with the embed source (url)
            $signature_request = (new SignatureRequest)->update([
                'id' => $signature_request->id,
                'url' => $embed_src
            ]);

            // hardcoded email for testing
            $email = 'phippsy@phippsy.tech';

            // EmailService::send([
            //     'to_email' => $email,
            //     'from_name' => 'Crystel Care',
            //     'from_email' => 'crystel@crystelcare.com.au',
            //     'subject' => 'Please Sign: Updated Service Agreement for ' . $data['participant_name'],
            //     'html' => (new ContentTemplate)->render([
            //         'template' => 'signing-link.html',
            //         'template_variables' => [
            //             'ACTION_URL' => $embed_src,
            //             'ORGANISATION_NAME' => 'Crystel Care',
            //             'ORGANISATION_ADDRESS' => '',
            //             'PARTICIPANT_NAME' => $data['participant_name'],
            //             'SUPPORT_URL' => 'https://crystelcare.com.au',
            //             'EMAIL_ADDRESS' => $email,
            //         ],
            //     ])
            // ]);
            return;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }

    function createActionJSPA(array $data): array
    {
        $jspa = [];

        $services = [];
        foreach ($data['services'] as $service) {
            $services[] = [
                'participant_service_id' => $service['participant_service_id'],
                'new_rate' => $service['new_rate'],
            ];
        }

        $jspa = [
            'action' => 'createServiceAgreementAmendment',
            'data' => [
                'service_agreement_id' => $data['service_agreement_id'],
                'amendment_start_date' => $data['amendment_start_date'],
                'services' => $services,
            ],
        ];

        return $jspa;
    }
}
