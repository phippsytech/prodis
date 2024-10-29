<?php

namespace NDISmate\Models\Participant\ServiceAgreement;

use RedBeanPHP\R;

class GenerateServiceAgreementFromDraft
{
    public function __invoke($data): array
    {
        $this->generateServiceAgreement($data);
        return [];
    }

    private function generateServiceAgreement($data): void
    {
        $query = '
            UPDATE serviceagreements
            SET is_draft = 0
            WHERE is_draft = 1 
            AND id=:id
        ';

        R::exec($query, [':id' => $data['id']]);
    }
}
