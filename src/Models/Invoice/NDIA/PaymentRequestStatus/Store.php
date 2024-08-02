<?php

namespace NDISmate\Models\Invoice\NDIA\PaymentRequestStatus;

use \RedBeanPHP\R as R;

class Store
{
    public function __invoke($obj, $file)
    {
        $binary = $file['data'];

        // Read the csv string and convert it into an array
        $csv = array_map('str_getcsv', explode("\n", $binary));
        $header = array_shift($csv);

        $expected_order = [
            'RegistrationNumber',
            'NDISNumber',
            'SupportsDeliveredFrom',
            'SupportsDeliveredTo',
            'SupportNumber',
            'ClaimReference',
            'Quantity',
            'UnitPrice',
            'GSTCode',
            'PaidTotalAmount',
            'Payment Request Number',
            'Participant Name',
            'Capped Price',
            'Payment Request Status',
            'Error Message',
            'ClaimType',
            'CancellationReason',
            'ABN of Support Provider',
        ];

        // I've had to switch to this because NDIA have changed the columns returned in the CSV file
        if (!$this->array_contains_keys_in_order($header, $expected_order)) {
            return ['http_code' => 400, 'error_message' => 'CSV file does not match expected format'];
        }

        foreach ($csv as $row) {
            if (count($row) == 1 && $row[0] == '') {  // skip blank lines
                continue;
            }
            unset($data);
            $data = array_combine($header, $row);

            // Convert date strings to DateTime objects
            $supportsDeliveredFromDate = \DateTime::createFromFormat('d/m/Y', $data['SupportsDeliveredFrom']);
            $supportsDeliveredToDate = \DateTime::createFromFormat('d/m/Y', $data['SupportsDeliveredTo']);

            // checking for existing record
            // try {
            $bean = R::findOrCreate(
                'ndiapaymentrequeststatuss',
                [
                    'registration_number' => $data['RegistrationNumber'],
                    'ndis_number' => $data['NDISNumber'],
                    'supports_delivered_from' => $supportsDeliveredFromDate->format('Y-m-d'),
                    'supports_delivered_to' => $supportsDeliveredToDate->format('Y-m-d'),
                    'support_number' => $data['SupportNumber'],
                    'claim_reference' => $data['ClaimReference'],
                    'quantity' => $data['Quantity'],
                    'unit_price' => $data['UnitPrice'],
                    'gst_code' => $data['GSTCode'],
                    'paid_total_amount' => $data['PaidTotalAmount'],
                    'payment_request_number' => $data['Payment Request Number'],
                    'participant_name' => $data['Participant Name'],
                    'capped_price' => $data['Capped Price'],
                    'claim_type' => $data['ClaimType'],
                    'cancellation_reason' => $data['CancellationReason'],
                    'abn_of_support_provider' => $data['ABN of Support Provider']
                ]
            );
            $bean->payment_request_status = $data['Payment Request Status'];
            $bean->error_message = $data['Error Message'];
            R::store($bean);
            // } catch (\Exception $e) {

            //     throw new \Exception($e->getMessage());
            // }
        }

        return ['http_code' => 201];
    }

    function array_contains_keys_in_order($array, $expected_order)
    {
        $is_in_order = true;
        foreach ($array as $i => $value) {
            if ($expected_order[$i] != $value) {
                $is_in_order = false;
                break;
            }
        }
        return $is_in_order;
    }
}
