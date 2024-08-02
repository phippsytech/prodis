<?php

namespace NDISmate\Xero;


use \RedBeanPHP\R as R;


class DeleteBatchPayment {

    function __invoke($obj){

        $batchPaymentDelete = new \XeroAPI\XeroPHP\Models\Accounting\BatchPaymentDelete;
        $batchPaymentDelete->setStatus('DELETED');
        $batchPaymentDelete->setBatchPaymentID('df27c8ee-95c0-435d-bf8b-70268c584266');

        try {
            $result = $obj->accountingApi->deleteBatchPayment($obj->tenant_id, $batchPaymentDelete);
            return ["http_code"=>200,"result"=>$result];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
    }
}