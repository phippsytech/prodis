<?php

namespace NDISmate\Services\ServiceAgreementService;

use NDISmate\Services\DocumentService\GenerateServiceAgreementAmendment;
use RedBeanPHP\R as R;

class GenerateServiceAgreementAmendments
{
    public function __invoke($data)
    {
        $result = (new GenerateServiceAgreementAmendment)([
            'service_agreement_id' => 1,
            'participant_name' => 'Agro',
            'ndis_number' => '4XX XXX XXX',
            'representative_name' => 'Jamie Dunn',
            'representative_phone' => '04XX XXX XXX',
            'representative_email' => 'jamie@agro.com.au',
            'service_agreement_start_date' => (new DateTime('2023-09-01'))->format('d/m/Y'),
            'service_agreement_end_date' => (new DateTime('2024-08-31'))->format('d/m/Y'),
            'amendment_start_date' => (new DateTime('2024-07-13'))->format('d/m/Y'),
            'services' => [
                [
                    'name' => 'Specialist Behaviour Intervention Support',
                    'original_rate' => 80,
                    'new_rate' => 100,
                ],
                [
                    'name' => 'Behaviour Management Plan Including Training in Behaviour Management Strategies',
                    'original_rate' => 80,
                    'new_rate' => 100,
                ],
                [
                    'name' => 'Service 3',
                    'original_rate' => 80,
                    'new_rate' => 100,
                ],
            ],
        ]);
    }
}
