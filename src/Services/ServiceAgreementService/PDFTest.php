<?php
namespace NDISmate\Services\ServiceAgreementService;
use NDISmate\CORE\ContentTemplate;

use NDISmate\CORE\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;


class PDFTest
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $data = [];


        $data['provider_name'] = 'Crystel Care Pty Ltd';
        $data['provider_short_name']= 'Crystel Care';
        $data['provider_abn'] = '36 662 942 403';
        $data['provider_contact'] = 'Tracey Jordan';
        $data['provider_contact_phone'] =  '0403 765 238';
        $data['provider_contact_email'] = 'tracey@crystelcare.com.au';

        $data['payment_method'] = 'EFT';
        $data['payment_period'] = '7 days';


        $data['participant_name'] = 'Brenton Williams';
        $data['participant_dob'] = '1990-01-01';
        $data['ndis_number'] = '123456789';
        $data['participant_phone'] = '123456789';
        $data['participant_email'] = 'email@email.com';
        $data['participant_address'] = 'Participant Address';
        $data['representative_name'] = 'Ashley Williams';
        $data['representative_phone'] = '123456789';
        $data['representative_email'] = 'email@email.com';
        $data['representative_address'] = 'Representative Address';
        $data['ndis_plan_start_date'] = '2021-01-01';
        $data['ndis_plan_end_date'] = '2021-12-31';
        $data['service_agreement_start_date'] = '2021-01-01';
        $data['service_agreement_end_date'] = '2021-12-31';
        $data['amendment_start_date'] = '2021-01-01';
        $data['services'] = [
            
            [
                'name' => 'Behaviour Mangagement Plan',
                'description' => '11_023_0110_7_3 Behavior Management Plan Including Training in Behaviour Management Strategies (Specialist Positive Behaviour Support)',
                'rate' => '244.22',
                'unit' => 'Hour',
                'quantity' => '23.03',
                'total' => '5625.71',
                'provider_travel' => false,
                'plan_manager' => 'Plan Manager',
            ],
            [
                'name' => 'Specialist Behavioural Intervention Support',
                'description' => '11_022_0110_7_3 Specialist Behavioural Intervention Support (Specialist Positive Behaviour Support)',
                'rate' => '244.22',
                'unit' => 'Hour',
                'quantity' => '54.13',
                'total' => '13220.91',
                'provider_travel' => true,
                'plan_manager' => 'Plan Manager',
            ],
        ];
















        $html = (new ContentTemplate)->render([
            'template' => 'service-agreement.html',
            'template_variables' => [
                'PROVIDER_NAME' => $data['provider_name'],
                'PROVIDER_SHORT_NAME' => $data['provider_short_name'],
                'PROVIDER_ABN' => $data['provider_abn'],
                'PROVIDER_CONTACT' => $data['provider_contact'],
                'PROVIDER_CONTACT_PHONE' => $data['provider_contact_phone'],
                'PROVIDER_CONTACT_EMAIL' => $data['provider_contact_email'],

                'PAYMENT_METHOD' => $data['payment_method'],
                'PAYMENT_PERIOD' => $data['payment_period'],

                'PARTICIPANT_NAME' => $data['participant_name'],
                'PARTICIPANT_DOB' => $data['participant_dob'],
                'NDIS_NUMBER' => $data['ndis_number'],
                'PARTICIPANT_PHONE' => $data['participant_phone'],
                'PARTICIPANT_EMAIL' => $data['participant_email'],
                'PARTICIPANT_ADDRESS' => $data['participant_address'],
                'REPRESENTATIVE_NAME' => $data['representative_name'],
                'REPRESENTATIVE_PHONE' => $data['representative_phone'],
                'REPRESENTATIVE_EMAIL' => $data['representative_email'],
                'NDIS_PLAN_START_DATE' => (new \DateTime($data['ndis_plan_start_date']))->format('j M Y'),
                'NDIS_PLAN_END_DATE' => (new \DateTime($data['ndis_plan_end_date']))->format('j M Y'),
                'SERVICE_AGREEMENT_START_DATE' => (new \DateTime($data['service_agreement_start_date']))->format('j M Y'),
                'SERVICE_AGREEMENT_END_DATE' => (new \DateTime($data['service_agreement_end_date']))->format('j M Y'),
                'AMENDMENT_START_DATE' => (new \DateTime($data['amendment_start_date']))->format('j M Y'),
                'CURRENT_DATE' => (new \DateTime())->format('j M Y'),
                'SERVICES' => $data['services'],
            ],
        ]);

        // HTML content to be converted to PDF
        $htmlContent = "<html><body>$html</body></html>";


        $footerContent = '<html>
<head>
    <style>
        body {
            font-size: 12px;
            padding: 0;
            box-sizing: border-box;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ccc;
        }
    </style>
</head>
<body>
<div class="footer">

<p>Crystel Care Service Agreement - Crystel Care Pty Ltd, NDIS registration # 4-FQGF1QZ
Crystel Care Pty Ltd operates under the NDIS registration of Autism Therapy</p>


<p>Page <span class="pageNumber"></span> of <span class="totalPages"></span></p>
</div>
</body>
</html>';


        // $response->getBody()->write($htmlContent);
        // return $response->withHeader('Content-Type', 'text/html');

    try {

          $client = new Client();

        // Sending the request to Gotenberg
        $gotenbergResponse = $client->request('POST', 'http://gotenberg:3000/forms/chromium/convert/html', [
            'multipart' => [
                [
                    'name'     => 'files',
                    'contents' => Psr7\Utils::streamFor($htmlContent),
                    'filename' => 'index.html', // Filename must be index.html
                ],
                [
                    'name'     => 'files',
                    'contents' => Psr7\Utils::streamFor($footerContent),
                    'filename' => 'footer.html', // Filename must be footer.html
                ],
                [
                    'name'     => 'paperWidth',
                    'contents' => '8.27', // A4 width in inches
                ],
                [
                    'name'     => 'paperHeight',
                    'contents' => '11.69', // A4 height in inches
                ],
            ],
        ]);

        // Setting the response headers for the PDF download
        // $response = $response->withHeader('Content-Type', 'application/pdf');
        // $response = $response->withHeader('Content-Disposition', 'attachment; filename="generated.pdf"');

        // Writing the PDF content to the response body
        $response = $response->withHeader('Content-Type', 'application/pdf');
        $response = $response->withHeader('Content-Disposition', 'inline; filename="generated.pdf"');


        // Writing the PDF content to the response body
        $response->getBody()->write($gotenbergResponse->getBody()->getContents());

        return $response;

    } catch (\Exception $e) {
        // Handle errors

                // $response->getBody()->write($htmlContent);
        // return $response->withHeader('Content-Type', 'text/html');

        return JsonResponse::internalServerError($e->getMessage());

        // return $response->withStatus(500)->withJson(['error' => $e->getMessage()]);
    }




    }
}
