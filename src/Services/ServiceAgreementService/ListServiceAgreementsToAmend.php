<?php
namespace NDISmate\Services\ServiceAgreementService;

use RedBeanPHP\R as R;

class ListServiceAgreementsToAmend
{
    public function __invoke($data)
    {
        try {
            $query =
                'SELECT
                    clientplanservices.id as participant_service_id,
                    clientplans.service_agreement_signed_date,
                    clientplans.service_agreement_end_date,
                    clients.name as participant_name,
                    clients.ndis_number,
                    stakeholders.name as representative_name, 
                    stakeholders.email as representative_email,
                    stakeholders.phone as representative_phone,
                    clientplanservices.plan_id as service_agreement_id,
                    clientplanservices.service_id,
                    services.code,
                    clientplanservices.budget,
                    services.billing_code,
                    services.rate,
                    supportitems.sa AS support_item_rate,
                    clientplanservices.is_active,
                    supportitems.sa - services.rate AS difference,
                    supportitems.support_item_name
                FROM clientplanservices
                join clients on clients.id = clientplanservices.participant_id
                join stakeholders on (stakeholders.client_id = clients.id and stakeholders.representative = 1)
                join clientplans on clientplans.id = clientplanservices.plan_id
                JOIN services ON services.id = clientplanservices.service_id
                JOIN supportitems ON services.billing_code = supportitems.support_item_number
                    AND supportitems.start_date = (
                        SELECT MAX(si.start_date)
                        FROM supportitems si
                        WHERE si.support_item_number = supportitems.support_item_number
                    )
                WHERE
                        clientplanservices.is_active = 1
                    and services.rate != supportitems.sa
                    and clientplans.is_active = 1
                    and clientplans.service_agreement_end_date > CURDATE()
                    and (clients.on_hold IS NULL OR clients.on_hold != 1)
                    and (clients.archived IS NULL OR clients.archived != 1)
                    and clients.ndis_plan_end_date > CURDATE()
                    
                ORDER BY
                    clientplanservices.participant_id,
                    clientplanservices.id';

            if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
                $query = str_replace('ANY_VALUE', '', $query);
            }

            $beans = R::getAll(
                $query
            );

            return $beans;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
