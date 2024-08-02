<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class ListUnbilledLineItems
{
    public function __invoke($data)
    {
        // I HAVE MISSED THE CASE WHERE THE SERVICE DOES NOT HAVE A PLAN MANAGER. THIS IS A BUG
        // TO RESOLVE THIS ALL CLIENT SERVICES NOW HAVE A PLAN MANAGER.

        $metrics['start'] = microtime(true);

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
            '4050094011' as RegistrationNumber,
            TRIM(REPLACE(clients.ndis_number, ' ', '')) as NDISNumber,
            timetrackings.session_date as SupportsDeliveredFrom,
            timetrackings.session_date as SupportsDeliveredTo,
            services.billing_code as SupportNumber,
            CONCAT(TRIM(REPLACE(clients.ndis_number, ' ', '')),'-',:invoice_batch) as ClaimReference,
            ROUND(
                    (
                        timetrackings.session_duration 
                        + IFNULL(
                            (CASE WHEN timetrackings.actual_travel_time > 30 THEN 30 ELSE timetrackings.actual_travel_time END)
                            ,0
                        )
                    ) / 60
                , 2
            ) AS Quantity,
            ROUND(timetrackings.session_duration,2) as SessionDuration, 
            timetrackings.rate as UnitPrice,
            services.billing_unit as ServiceBillingUnit,
            clients.name as ClientName,
            services.code as ServiceCode,
            services.name as ServiceName,
            timetrackings.id as SessionId,
            timetrackings.claim_type as ClaimType,
            timetrackings.cancellation_reason as CancellationReason,
            timetrackings.service_id as ServiceId,
            timetrackings.staff_id as StaffId,
            timetrackings.client_id as ClientId,
            timetrackings.planmanager_id as PlanManagerId,
            planmanagers.id as ClientBillingId,
            planmanagers.name as ClientBillingName,
            planmanagers.email as ClientBillingEmail,
            staffs.name as TherapistName,
            timetrackings.invoice_batch as InvoiceBatch,
            timetrackings.trip_id as TripId,
            timetrackings.unit_type as UnitType
        FROM timetrackings
            JOIN clients on (clients.id=timetrackings.client_id)
            JOIN services on (timetrackings.service_id= services.id)
            JOIN staffs on (timetrackings.staff_id= staffs.id)
            JOIN planmanagers on (planmanagers.id=timetrackings.planmanager_id)  
            left join billablendiaerrors on (billablendiaerrors.timetracking_id=timetrackings.id)
            WHERE billablendiaerrors.timetracking_id is null AND timetrackings.invoice_batch " . ($data['invoice_batch'] == null ? 'IS NULL' : '=:invoice_batch') . ' 
             ' . (isset($data['planmanager_id']) ? ' AND planmanagers.id=:planmanager_id ' : '') . '
             ' . (isset($data['ndis_number']) ? " AND TRIM(REPLACE(clients.ndis_number, ' ', ''))=:ndis_number " : '') . ' 
            HAVING Quantity > 0
        ORDER BY NDISNumber, PlanManagerId, SupportNumber, SupportsDeliveredFrom   
';

        $bean = R::getAll($query, $query_parameters);
        // echo (new \NDISmate\Utilities\InterpolateQuery)($query, $query_parameters);

        // Adjust the Quantity to 1 where ServiceBillingUnit = "each"
        foreach ($bean as &$record) {
            if ($record['ServiceBillingUnit'] === 'each') {
                $record['Quantity'] = 1;
            }
            if ($record['ServiceBillingUnit'] == 'kms' || $record['UnitType'] == 'km') {
                $record['Quantity'] = $record['SessionDuration'];
            }
        }

        // Now we need to sort the array by NDISNumber
        // usort($bean, [$this, "compare"]);

        $bean = $this->sortArrayByFields($bean, ['ClientName', 'SupportsDeliveredFrom']);

        $metrics['end'] = microtime(true);

        $metrics['duration'] = ($metrics['end'] - $metrics['start']) . ' seconds';

        return ['http_code' => 200, 'result' => $bean, 'metrics' => $metrics];
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
