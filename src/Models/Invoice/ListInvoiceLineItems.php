<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class ListInvoiceLineItems
{
    public function __invoke($data)
    {
        // I HAVE MISSED THE CASE WHERE THE SERVICE DOES NOT HAVE A PLAN MANAGER. THIS IS A BUG
        // TO RESOLVE THIS ALL CLIENT SERVICES NOW HAVE A PLAN MANAGER.

        $query_parameters = [
            ':invoice_batch' => $data['invoice_batch']
        ];

        if (isset($data['ndis_number'])) {
            $query_parameters[':ndis_number'] = $data['ndis_number'];
        }

        if (isset($data['planmanager_id'])) {
            $query_parameters[':planmanager_id'] = $data['planmanager_id'];
        }

        $query = "SELECT
        groupedsessions.RegistrationNumber,
        groupedsessions.NDISNumber,
        groupedsessions.SupportsDeliveredFrom,
        groupedsessions.SupportsDeliveredTo,
        groupedsessions.SupportNumber,
        groupedsessions.ClaimType,
        groupedsessions.CancellationReason,
        groupedsessions.ClaimReference,
        groupedsessions.Quantity,
        groupedsessions.SessionDuration,
        groupedsessions.UnitPrice,
        groupedsessions.ClientId,
        groupedsessions.ClientName,
        groupedsessions.InvoiceBatch,
        groupedsessions.PlanManagerId,
        planmanagers.id as ClientBillingId,
        planmanagers.name as ClientBillingName,
        planmanagers.email as ClientBillingEmail,
        groupedsessions.ServiceCode,
        groupedsessions.ServiceName,
        groupedsessions.ServiceBillingUnit,
        staffs.name as TherapistName,
        groupedsessions.SessionId,
        groupedsessions.ServiceId
     FROM (
        SELECT 
            '4050094011' as RegistrationNumber,
            TRIM(REPLACE(clients.ndis_number, ' ', '')) as NDISNumber,
            timetrackings.session_date as SupportsDeliveredFrom,
            timetrackings.session_date as SupportsDeliveredTo,
            ANY_VALUE(services.billing_code) as SupportNumber,
            CONCAT(TRIM(REPLACE(clients.ndis_number, ' ', '')),'-',:invoice_batch) as ClaimReference,
            ROUND(
                SUM( 
                    (
                        timetrackings.session_duration 
                        + IFNULL(
                            (CASE WHEN timetrackings.actual_travel_time > 30 THEN 30 ELSE timetrackings.actual_travel_time END)
                            ,0
                        )
                    ) / 60
                    )
                , 2
            ) AS Quantity,
            ROUND(SUM(timetrackings.session_duration),2) as SessionDuration, 
            ANY_VALUE(services.rate) as UnitPrice,
            ANY_VALUE(services.billing_unit) as ServiceBillingUnit,
            ANY_VALUE(clients.name) as ClientName,
            ANY_VALUE(services.code) as ServiceCode,
            ANY_VALUE(services.name) as ServiceName,
            ANY_VALUE(timetrackings.id) as SessionId,
            ANY_VALUE(timetrackings.claim_type) as ClaimType,
            ANY_VALUE(timetrackings.cancellation_reason) as CancellationReason,
            ANY_VALUE(timetrackings.service_id) as ServiceId,
            ANY_VALUE(timetrackings.staff_id) as StaffId,
            ANY_VALUE(timetrackings.client_id) as ClientId,
            ANY_VALUE(timetrackings.planmanager_id) as PlanManagerId,
            ANY_VALUE(timetrackings.invoice_batch) as InvoiceBatch
        FROM timetrackings
            JOIN clients on (clients.id=timetrackings.client_id)
            JOIN services on (timetrackings.service_id= services.id)
            WHERE timetrackings.invoice_batch " . ($data['invoice_batch'] == null ? 'is null' : '=:invoice_batch')
            . (isset($data['ndis_number']) ? ' AND clients.ndis_number=:ndis_number ' : '')
            . (isset($data['planmanager_id']) ? ' AND timetrackings.planmanager_id=:planmanager_id ' : '')
            . ' GROUP BY NDISNumber, ClaimReference, services.billing_code, ClaimType, timetrackings.session_date
        HAVING Quantity > 0
    ) as groupedsessions
    
    JOIN staffs on (groupedsessions.StaffId= staffs.id)
    JOIN planmanagers on (planmanagers.id=groupedsessions.PlanManagerId)  
    ORDER BY groupedsessions.NDISNumber, groupedsessions.PlanManagerId, groupedsessions.SupportNumber, groupedsessions.SupportsDeliveredFrom
';

        // echo (new \NDISmate\Utilities\InterpolateQuery)($query, $query_parameters);

        if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
            $query = str_replace('ANY_VALUE', '', $query);
        }

        $bean = R::getAll($query, $query_parameters);

        $travel_query = "SELECT
                '4050094011' as RegistrationNumber,
                TRIM(REPLACE(clients.ndis_number, ' ', '')) as NDISNumber,
                timetrackings.session_date as SupportsDeliveredFrom,
                timetrackings.session_date as SupportsDeliveredTo,
                '15_799_0128_1_3' as SupportNumber,
                'TRAN' as ClaimType,
                null as CancellationReason,
                CONCAT(TRIM(REPLACE(clients.ndis_number, ' ', '')),'-',:invoice_batch) as ClaimReference,
                ROUND(SUM( timetrackings.kilometers_travelled ),2) AS Quantity,
                1.00 as UnitPrice,
                ANY_VALUE(clients.id) as ClientId,
                ANY_VALUE(clients.name) as ClientName,
                ANY_VALUE(timetrackings.planmanager_id) as PlanManagerId,
                ANY_VALUE(planmanagers.id) as ClientBillingId,
                ANY_VALUE(planmanagers.name) as ClientBillingName,
                ANY_VALUE(planmanagers.email) as ClientBillingEmail,
                'TRAVEL' as ServiceCode,
                'Travel' as ServiceName,
                ANY_VALUE(services.billing_unit) as ServiceBillingUnit,
                ANY_VALUE(staffs.name) as TherapistName,
                CONCAT(ANY_VALUE(timetrackings.id),'') as SessionId,
                ANY_VALUE(timetrackings.invoice_batch) as InvoiceBatch
            FROM timetrackings
            JOIN clients on (clients.id=timetrackings.client_id)
            JOIN services on (timetrackings.service_id= services.id)
            JOIN staffs on (timetrackings.staff_id= staffs.id)
            JOIN planmanagers on (planmanagers.id=timetrackings.planmanager_id)
            WHERE timetrackings.invoice_batch " . ($data['invoice_batch'] == null ? 'is null' : '=:invoice_batch')
            . (isset($data['ndis_number']) ? ' AND clients.ndis_number=:ndis_number ' : '')
            . (isset($data['planmanager_id']) ? ' AND timetrackings.planmanager_id=:planmanager_id ' : '')
            . ' AND timetrackings.kilometers_travelled > 0
            GROUP BY NDISNumber, session_date, ClaimReference
            ORDER BY  NDISNumber, ANY_VALUE(timetrackings.planmanager_id), session_date
        ';

        // echo (new \NDISmate\Utilities\InterpolateQuery)($travel_query, $query_parameters);

        if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
            $travel_query = str_replace('ANY_VALUE', '', $travel_query);
        }

        $travel_bean = R::getAll($travel_query, $query_parameters);

        $bean = array_merge($bean, $travel_bean);

        // Adjust the Quantity to 1 where ServiceBillingUnit = "each"
        foreach ($bean as &$record) {
            if ($record['ServiceBillingUnit'] === 'each') {
                $record['Quantity'] = 1;
            }
            if ($record['ServiceBillingUnit'] == 'kms') {
                $record['Quantity'] = $record['SessionDuration'];
            }
        }

        // Now we need to sort the array by NDISNumber
        // usort($bean, [$this, "compare"]);

        $bean = $this->sortArrayByFields($bean, ['ClientName', 'SupportsDeliveredFrom']);

        return ['http_code' => 200, 'result' => $bean];
    }

    function compare($a, $b)
    {
        return strcmp($a['NDISNumber'], $b['NDISNumber']);
    }

    function sortArrayByFields($array, $fields)
    {
        usort($array, function ($a, $b) use ($fields) {
            foreach ($fields as $field) {
                if ($a[$field] !== $b[$field]) {
                    return $a[$field] <=> $b[$field];
                }
            }
            return 0;
        });
        return $array;
    }
}
