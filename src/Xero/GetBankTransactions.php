<?php

namespace NDISmate\Xero;


use \RedBeanPHP\R as R;

/*
      "Type": "RECEIVE",
      "Contact": {
        "ContactID": "5df003ee-189a-4168-ad74-130e2ac1503a",
        "Name": "NDIS",
        "ContactPersons": [],
        "Addresses": [],
        "Phones": [],
        "ContactGroups": [],
        "HasAttachments": false,
        "HasValidationErrors": false
      },
      "LineItems": [],
      "BankAccount": {
        "Code": "680",
        "Name": "Operations Account",
        "AccountID": "09684963-a191-4bc5-b4e8-06a3b8eb75d0",
        "HasAttachments": false
      },
      "IsReconciled": true,
      "Date": "/Date(1684195200000+0000)/",
      "CurrencyCode": "AUD",
      "Status": "AUTHORISED",
      "LineAmountTypes": "Inclusive",
      "SubTotal": 945.76,
      "TotalTax": 0,
      "Total": 945.76,
      "BankTransactionID": "c8c7a0d8-72a3-45fb-a065-67d48092975e",
      "UpdatedDateUTC": "/Date(1684284613343+0000)/",
      "HasAttachments": false

*/
class GetBankTransactions {

    function __invoke($obj){

        $ifModifiedSince = new \DateTime("2020-02-06T12:17:43.202-08:00");
        // $where = 'Status=="' . XeroAPI\XeroPHP\Models\Accounting\BankTransaction::STATUS_AUTHORISED . '"';
        // $where = 'TYPE=="RECEIVE" AND Contact.Name=="NDIS"';
        // $where = 'Type=="RECEIVE" AND BankAccount.Code=="680" AND  Status=="AUTHORISED"';
        $where = 'Type=="RECEIVE" AND Contact.Name=="NDIS" AND Status=="AUTHORISED"';
        $order = "UpdatedDateUTC DESC";
        
        try {
            $apiResponse = $obj->accountingApi->getBankTransactions($obj->tenant_id, null, $where, $order);
            $bank_transactions = $apiResponse->getBankTransactions();
            // print_r($bank_transactions);
            foreach($bank_transactions as $bank_transaction){
                unset($contact);
                // $contact = $bank_transaction->getContact();
                // if(!empty($contact)) $result['ContactName'] = $contact->getName();
                $result['Date'] = $bank_transaction->getDateAsDate()->format('Y-m-d');
                $result['BankTransactionID'] = $bank_transaction->getBankTransactionId();
                $result['Total'] = $bank_transaction->getTotal();
                $result['Status'] = $bank_transaction->getStatus();
                $result['IsReconciled'] = $bank_transaction->getIsReconciled();
                $results[] = $result;
            
            }


            return ["http_code"=>200,"result"=>$results];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
    }
}