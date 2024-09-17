<?php
namespace NDISmate\Models\Client;

use RedBeanPHP\R;
use NDISmate\Utilities\LogActivity;

class HoldClientsWithoutActiveAgreements
{
    public function __invoke(): array
    {
        $this->holdClientsWithoutActiveAgreements();
        return [];
    }

    private function holdClientsWithoutActiveAgreements(): void
    {
        $query = '
            SELECT c.id
            FROM clients c
            LEFT JOIN clientplans cp ON c.id = cp.client_id AND cp.is_active = 1
            GROUP BY c.id
            HAVING COUNT(cp.id) = 0;
        ';
        
        $clientIds = R::getCol($query);

        foreach ($clientIds as $clientId) {
            
            $affectedRows = R::exec('UPDATE clients SET on_hold = 1 WHERE id = ? AND on_hold <> 1', [$clientId]);

            if ($affectedRows > 0) {
                LogActivity::log($clientId, 'on-hold', 'participant', 'Participant has been placed on hold due to all service agreements being inactive or expired.');
            }
        }
    }
}