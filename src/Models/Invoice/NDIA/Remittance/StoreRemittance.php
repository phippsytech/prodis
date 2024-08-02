<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class StoreRemittance
{
    public function __invoke($obj, $file)
    {
        $remittance_csv_name = $file['name'];

        if (empty($remittance_csv_name)) {
            return ['http_code' => 400, 'error_message' => 'No file name provided'];
        }

        $beans = R::find('ndiapaymentremittances', 'remittance_csv_name=:remittance_csv_name', [':remittance_csv_name' => $remittance_csv_name]);

        if (count($beans) != 0) {
            return ['http_code' => 400, 'error_message' => $remittance_csv_name . ' has already been uploaded'];
        }

        $binary = $file['data'];

        // Read the csv string and convert it into an array
        $csv = array_map('str_getcsv', explode("\n", $binary));
        $header = array_shift($csv);

        $expected_order = ['PayeeBP', 'Z4No', 'FinYrs', 'PayReqNum', 'PayReqDocDate', 'ProvClaimRef', 'ItemID', 'ItemQty', 'UnitPrice', 'AmountClaimed', 'AmountPaid', 'ParticipantBP', 'ParticipantName', 'SupportStartDate', 'SupportEndDate', 'ServiceBookingNum', 'BulkClmId', 'ClaimType', 'CancelRsn'];

        // I've had to switch to this because NDIA have changed the columns returned in the CSV file
        if (!$this->array_contains_keys_in_order($header, $expected_order)) {
            return ['http_code' => 400, 'error_message' => $remittance_csv_name . ' does not match expected format'];
        }

        foreach ($csv as $row) {
            if (count($row) == 1 && $row[0] == '') {  // skip blank lines
                continue;
            }
            unset($data);
            $data = array_combine($header, $row);

            $bean = R::findOrCreate('ndiapaymentremittances', ['payment_request_number' => $data['PayReqNum']]);
            $bean->claim_date = $data['PayReqDocDate'];
            $bean->supports_delivered_from = $data['SupportStartDate'];
            $bean->supports_delivered_to = $data['SupportEndDate'];
            $bean->invoice_number = $data['ProvClaimRef'];
            $bean->billing_code = $data['ItemID'];
            $bean->quantity = $data['ItemQty'];
            $bean->unit_price = $data['UnitPrice'];
            $bean->amount_paid = $data['AmountPaid'];
            $bean->ndis_number = $data['ParticipantBP'];
            $bean->claim_type = $data['ClaimType'];
            $bean->cancel_reason = $data['CancelRsn'];
            $bean->upload_csv_name = $data['BulkClmId'];
            $bean->remittance_csv_name = $remittance_csv_name;
            R::store($bean);
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
