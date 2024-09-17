<?php

namespace NDISmate\Models\Participant\ServiceAgreement;

use RedBeanPHP\R;

class MakeExpiredServiceAgreementsInactive
{
    public function __invoke(): array
    {
        $this->deactivateExpiredServiceAgreements();
        return [];
    }

    private function deactivateExpiredServiceAgreements(): void
    {
        $query = '
            UPDATE serviceagreements
            SET is_active = 0
            WHERE is_active = 1 
            AND service_agreement_end_date < CURDATE()
        ';

        R::exec($query);
    }
}
