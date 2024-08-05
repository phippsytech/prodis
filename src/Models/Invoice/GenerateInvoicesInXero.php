<?php
namespace NDISmate\Models\Invoice;

use NDISmate\Xero\Helpers as XeroHelpers;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class GenerateInvoicesInXero
{
    public function __invoke($data)
    {
        $xeroHelpers = new XeroHelpers;

        // Check that the appropriate items are in Xero
        // https://go.xero.com/app/!Pqzj1/products-and-services
        // $xeroHelpers->setupItems();

        // Get Line Items
        $invoice_line_items = (new \NDISmate\Models\Invoice\ListInvoiceAggregatedLineItems)($data);

        // Initialize an empty result array
        $result = array();

        // Loop through the data array
        foreach ($invoice_line_items['result'] as $item) {
            // Group by NDISNumber
            $ndisNumber = $item['NDISNumber'];
            if (!isset($result[$ndisNumber])) {
                $result[$ndisNumber] = array();
            }

            // Group by PlanManagerId within each NDISNumber group
            $planManagerId = $item['PlanManagerId'];
            if (!isset($result[$ndisNumber][$planManagerId])) {
                $result[$ndisNumber][$planManagerId] = array();
            }

            // Add the item to the result array
            $result[$ndisNumber][$planManagerId][] = $item;
        }

        $invoices = [];

        $invoice_batch_date = $this->getInvoiceBatchDate($data['invoice_batch']);
        $invoice_due_date = date('Y-m-d', strtotime($invoice_batch_date . '+7 days'));

        foreach ($result as $client) {
            foreach ($client as $plan_manager) {
                // TODO: Stop using hard coded numbers for NDIA Plan Manager IDs
                // skip generating NDIA invoices in Xero
                if ($plan_manager[0]['PlanManagerId'] == 45) {
                    continue;
                }

                $xero_contact_id = $xeroHelpers->findOrCreateXeroContactByPlanManagerId([
                    'plan_manager_id' => $plan_manager[0]['PlanManagerId'],
                    'email' => $plan_manager[0]['ClientBillingEmail'],
                    'name' => $plan_manager[0]['ClientBillingName']
                ]);

                $invoice = [
                    'account_code' => 200,
                    'date' => $invoice_batch_date,
                    'due_date' => $invoice_due_date,
                    'reference' => $plan_manager[0]['ClientName'] . ' - ' . $plan_manager[0]['NDISNumber'],
                    'contact_id' => $xero_contact_id,
                    'invoice_number' => $plan_manager[0]['NDISNumber'] . '-' . $plan_manager[0]['ClientBillingId'] . '-' . $data['invoice_batch'],
                ];

                // TODO: Stop using hard coded numbers for NDIA Plan Manager IDs
                // this marks NDIA Invoices as sent.
                if ($plan_manager[0]['PlanManagerId'] == 45) {
                    $invoice['sent_to_contact'] = true;
                } else {
                    $invoice['sent_to_contact'] = false;
                }

                foreach ($plan_manager as $row) {
                    // Set the claim type based on the value in the current row of the database result set.
                    // If the claim type is "CANC", append the cancellation reason to the claim type string.
                    $claim_type = '';  // unset the variable in case it was set in a previous iteration of the loop
                    if (!empty($row['ClaimType'])) {
                        $claim_type = '- Claim Type: ' . $row['ClaimType'];
                        if ($row['ClaimType'] == 'CANC') {
                            $claim_type .= ' Cancellation Reason: ' . $row['CancellationReason'];
                        }
                    }

                    $account_code = $this->getXeroAccountCode($plan_manager[0]['NDISNumber'], $row['SupportNumber']);

                    // standard description
                    $description = $row['SupportNumber'] . ' - ' . $row['ServiceName'] . ' ' . date('d/m/Y', strtotime($row['SupportsDeliveredFrom'])) . ' - Therapist: ' . $row['TherapistName'] . ' ' . $claim_type;

                    // sil description without a therapist name
                    if ($account_code != 200) {
                        $description = $row['SupportNumber'] . ' - ' . $row['ServiceName'] . ' ' . date('d/m/Y', strtotime($row['SupportsDeliveredFrom'])) . ' ' . $claim_type;
                    }

                    $invoice['line_items'][] = [
                        'account_code' => $account_code,
                        'therapist_name' => $row['TherapistName'],
                        'item_code' => $row['ServiceCode'],  // service_code
                        'description' => $description,
                        'quantity' => $row['Quantity'],  // in decimal, not hours
                        'unit_amount' => $row['UnitPrice']  // service rate
                    ];
                }

                $invoices[] = $invoice;
                unset($invoice);
            }
        }

        // return ['http_code' => 201, 'result' => $invoices];

        $result = $xeroHelpers->createInvoices($invoices);

        return ['http_code' => 201, 'result' => $result];
    }

    function getInvoiceBatchDate($invoice_batch)
    {
        $result = R::getRow(
            'SELECT 
                generation_date
            FROM invoicebatchs
            WHERE id=:invoice_batch
            ',
            [
                ':invoice_batch' => $invoice_batch
            ]
        );
        return $result['generation_date'];
    }

    function getXeroAccountCode($ndis_number, $billing_code)
    {
        $xero_account_code = R::getCell(
            'SELECT xero_account_code 
            FROM clientplanservices
            JOIN clientplans ON (clientplans.id = clientplanservices.plan_id)
            JOIN clients ON (clients.id = clientplans.client_id)
            JOIN services ON (services.id = clientplanservices.service_id)
            WHERE clients.ndis_number=:ndis_number 
            AND services.billing_code=:billing_code',
            [
                ':ndis_number' => [$ndis_number, \PDO::PARAM_STR],  // force redbean to treat ndis_number as a string
                ':billing_code' => $billing_code
            ]
        );

        // return a default account code if no account code is found
        if (!$xero_account_code)
            return '200';

        return $xero_account_code;
    }
}
