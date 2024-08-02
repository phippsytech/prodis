<?php

namespace NDISmate\Models\Invoice\NDIA\PaymentRequestStatus;

use \RedBeanPHP\R as R;

class GetErrors
{
    public function __invoke()
    {
        // Query to get all errors for a specific invoice batch

        $paymentRequestNumbers = R::getAll('SELECT DISTINCT payment_request_id FROM billablendiaerrors WHERE payment_request_id IS NOT NULL');

        $result = [];  // Initialize the result array

        foreach ($paymentRequestNumbers as $item) {
            $paymentRequestId = $item['payment_request_id'];
            $paymentRequest = R::getRow(
                'SELECT 
                    id,
                    ndis_number,
                    supports_delivered_from,
                    supports_delivered_to,
                    support_number,
                    claim_reference,
                    quantity,
                    unit_price,
                    paid_total_amount,
                    payment_request_number,
                    capped_price,
                    claim_type,
                    cancellation_reason,
                    payment_request_status,
                    error_message
                FROM ndiapaymentrequeststatuss 
                WHERE id=:payment_request_id',
                [':payment_request_id' => $paymentRequestId]
            );

            // Fetch all timetracking_ids for the current payment_request_number
            $timeTrackingIds = R::getCol(
                'SELECT timetracking_id 
                FROM billablendiaerrors 
                WHERE payment_request_id=:payment_request_id',
                [':payment_request_id' => $paymentRequestId]
            );

            // print_r($timeTrackingIds);
            unset($billable, $billables);
            foreach ($timeTrackingIds as $timeTrackingId) {
                $billable = R::getRow(
                    "SELECT 
                        TRIM(REPLACE(clients.ndis_number, ' ', '')) as NDISNumber,
                        timetrackings.session_date as SupportsDeliveredFrom,
                        timetrackings.session_date as SupportsDeliveredTo,
                        services.billing_code as SupportNumber,
                        ROUND(timetrackings.session_duration/60,2) as Quantity, 
                        services.rate as UnitPrice,
                        services.billing_unit as ServiceBillingUnit,
                        clients.name as ClientName,
                        services.code as ServiceCode,
                        services.name as ServiceName,
                        timetrackings.id as SessionId,
                        timetrackings.claim_type as ClaimType,
                        timetrackings.cancellation_reason as CancellationReason,
                        timetrackings.service_id as ServiceId,
                        timetrackings.client_id as ClientId,
                        timetrackings.trip_id as TripId
                    FROM timetrackings 
                    JOIN clients on (clients.id=timetrackings.client_id)
                    JOIN services on (timetrackings.service_id= services.id)
                    WHERE timetrackings.id=:timetracking_id",
                    [':timetracking_id' => $timeTrackingId]
                );
                $billables[] = $billable;
            }
            $paymentRequest['billables'] = $billables;
            // Add to result array
            $result[] = $paymentRequest;
        }

        return !empty($result)
            ? ['http_code' => 200, 'result' => array_values($result)]
            : ['http_code' => 404, 'error_message' => 'No unhandled NDIA errors found.'];
    }
}
