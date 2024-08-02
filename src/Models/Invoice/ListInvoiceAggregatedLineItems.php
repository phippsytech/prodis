<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class ListInvoiceAggregatedLineItems
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
        groupedsessions.TripId,
        groupedsessions.UnitType,
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
            timetrackings.invoice_batch,
            '4050094011' as RegistrationNumber,
            TRIM(REPLACE(clients.ndis_number, ' ', '')) as NDISNumber,
            timetrackings.session_date as SupportsDeliveredFrom,
            timetrackings.session_date as SupportsDeliveredTo,
            services.billing_code as SupportNumber,
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
            ANY_VALUE(timetrackings.rate) as UnitPrice,
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
            ANY_VALUE(timetrackings.invoice_batch) as InvoiceBatch,
            ANY_VALUE(timetrackings.trip_id) as TripId,
            ANY_VALUE(timetrackings.unit_type) as UnitType
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
     '
            . (isset($data['client_ids']) ? ' WHERE groupedsessions.ClientID in (' . implode(',', $data['client_ids']) . ')' : '') . ' 
    ORDER BY groupedsessions.NDISNumber, groupedsessions.PlanManagerId, groupedsessions.SupportNumber, groupedsessions.SupportsDeliveredFrom';

        if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
            $query = str_replace('ANY_VALUE', '', $query);
        }

        $bean = R::getAll($query, $query_parameters);

        // Adjust the Quantity to 1 where ServiceBillingUnit = "each"
        foreach ($bean as &$record) {
            if ($record['ServiceBillingUnit'] === 'each') {
                $record['Quantity'] = 1;
            }
            if ($record['ServiceBillingUnit'] == 'kms' || $record['UnitType'] == 'km') {
                $record['Quantity'] = $record['SessionDuration'];
            }
        }

        $bean = $this->sortArrayByFields($bean, ['ClientName', 'SupportsDeliveredFrom']);

        return ['http_code' => 200, 'result' => $bean];
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
