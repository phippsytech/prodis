<?php
namespace NDISmate\Models\Invoice;

use NDISmate\Xero\Helpers as XeroHelpers;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class GetNDIABulkCSV
{
    public function __invoke($data)
    {
        $data['planmanager_id'] = 45;  // TODO: This should not be hardcoded

        $result = (new \NDISmate\Models\Invoice\ListInvoiceAggregatedLineItems)($data);
        $bean = $result['result'];

        if (empty($bean)) {
            return ['http_code' => 404, 'error_message' => 'No NDIA data to download for the given invoice batch.'];
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="file.csv"');

        $fp = fopen('php://memory', 'w');

        // NOTE: I am using fwrite() instead of fputcsv() because fputcsv() did not allow me to wrap YYYY-MM-DD in inverted commas which resulted in the date format being mangled if opened and saved in excel.

        fwrite($fp, "\"RegistrationNumber\",\"NDISNumber\",\"SupportsDeliveredFrom\",\"SupportsDeliveredTo\",\"SupportNumber\",\"ClaimReference\",\"Quantity\",\"Hours\",\"UnitPrice\",\"GSTCode\",\"AuthorisedBy\",\"ParticipantApproved\",\"InKindFundingProgram\",\"ClaimType\",\"CancellationReason\",\"ABN of Support Provider\"\n");

        foreach ($bean as $fields) {
            fwrite($fp,
                $fields['RegistrationNumber'] . ','
                    . $fields['NDISNumber'] . ','
                    . $fields['SupportsDeliveredFrom'] . ','
                    . $fields['SupportsDeliveredTo'] . ','
                    . $fields['SupportNumber'] . ','
                    . $fields['ClaimReference'] . ','
                    . $fields['Quantity'] . ','
                    . ','  // Hours.  sending this empty
                    . $fields['UnitPrice'] . ','
                    . 'P2,'  // GSTCode
                    . ','  // AuthorisedBy
                    . ','  // ParticipantApproved
                    . ','  // InKindFundingProgram
                    . $fields['ClaimType'] . ','  // ClaimType
                    . $fields['CancellationReason'] . ','  // CancellationReason
                    . '36662942403' . "\n"  // ABN of Support Provider
            // 36662942403 = Crystel Care Pty Ltd
            // 79253351418 = Autism Therapy Australia PTY LTD
            // TODO: HARD CODED DATA
            );
        }

        rewind($fp);
        $csv = stream_get_contents($fp);

        fclose($fp);
        readfile($csv);
        return ['http_code' => 200, 'result' => $csv];
    }
}
