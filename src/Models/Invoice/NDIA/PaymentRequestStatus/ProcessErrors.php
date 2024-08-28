<?php

namespace NDISmate\Models\Invoice\NDIA\PaymentRequestStatus;

use RedBeanPHP\R;

class ProcessErrors
{
    public function __invoke(): array
    {
        $paymentErrors = R::getAll(
            "SELECT pr.claim_reference, pr.id AS payment_request_id, tt.id AS timetracking_id
            FROM ndiapaymentrequeststatuss pr
            JOIN 
                clients c ON c.ndis_number = pr.ndis_number
            JOIN
                timetrackings tt ON 
                    tt.client_id = c.id
                    AND tt.session_date = pr.supports_delivered_from
                    AND COALESCE(NULLIF(tt.claim_type, ''), '') = COALESCE(NULLIF(pr.claim_type, ''), '')
                    AND COALESCE(NULLIF(tt.cancellation_reason, ''), '') = COALESCE(NULLIF(pr.cancellation_reason, ''), '')
                    AND tt.session_duration = pr.quantity
                    AND tt.rate = pr.unit_price
            WHERE pr.payment_request_status = 'ERROR'
                AND pr.claim_reference LIKE '%-300'"
        );

        $billableIds = [];
        $rejectedLineItems = []; 

        foreach ($paymentErrors as $paymentError) {
            $this->storeError($paymentError);
            $billableIds[] = $paymentError['timetracking_id'];
        }

        $billableIds = array_unique($billableIds);
        return ['http_code' => 201, 'data' => $billableIds];

        $this->reverseBillables($billableIds);

        return ['http_code' => 201, 'data' => [$rejectedLineItems, $billableIds]];
    }

    /**
     * Store the errors for line items.
     *
     * @param array $lineItem
     * @return void
     */
    private function storeError(array $lineItem): void
    {
        $bean = R::findOrCreate(
            'billablendiaerrors',
            [
                'timetracking_id' => $lineItem['timetracking_id'],
                'payment_request_id' => $lineItem['payment_request_id']
            ]
        );

        R::store($bean);
    }

    /**
     * Reverse billables based on given IDs.
     *
     * @param array $ids
     * @return void
     */
    private function reverseBillables(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE timetrackings SET invoice_batch=null WHERE id IN ($placeholders)";
        R::exec($sql, $ids);
    }
}