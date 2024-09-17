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
                    servicebookings.id as service_booking_id,
                    serviceagreements.service_agreement_signed_date,
                    serviceagreements.service_agreement_end_date,
                    clients.name as participant_name,
                    clients.ndis_number,
                    stakeholders.name as representative_name, 
                    stakeholders.email as representative_email,
                    stakeholders.phone as representative_phone,
                    servicebookings.plan_id as service_agreement_id,
                    servicebookings.service_id,
                    services.code,
                    servicebookings.budget,
                    services.billing_code,
                    services.rate,
                    supportitems.sa AS support_item_rate,
                    servicebookings.is_active,
                    supportitems.sa - services.rate AS difference,
                    supportitems.support_item_name
                FROM servicebookings
                join clients on clients.id = servicebookings.participant_id
                join stakeholders on (stakeholders.client_id = clients.id and stakeholders.representative = 1)
                join serviceagreements on serviceagreements.id = servicebookings.plan_id
                JOIN services ON services.id = servicebookings.service_id
                JOIN supportitems ON services.billing_code = supportitems.support_item_number
                    AND supportitems.start_date = (
                        SELECT MAX(si.start_date)
                        FROM supportitems si
                        WHERE si.support_item_number = supportitems.support_item_number
                    )
                WHERE
                        servicebookings.is_active = 1
                    and services.rate != supportitems.sa
                    and serviceagreements.is_active = 1
                    and serviceagreements.service_agreement_end_date > CURDATE()
                    and (clients.on_hold IS NULL OR clients.on_hold != 1)
                    and (clients.archived IS NULL OR clients.archived != 1)
                    and clients.ndis_plan_end_date > CURDATE()
                    
                ORDER BY
                    servicebookings.participant_id,
                    servicebookings.id';

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
