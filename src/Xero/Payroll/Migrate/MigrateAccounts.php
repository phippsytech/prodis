<?php
namespace NDISmate\Xero\Payroll\Migrate;

use \RedBeanPHP\R as R;


class MigrateAccounts {

    function __invoke($xero){

        // try {
            // SOURCE
            $source_accounts = $xero->accountingApi->getAccounts($xero->source_accounts_tenant_id);

            foreach ($source_accounts->getAccounts() as $source_account) {
                
                $source_account->setAccountId(null);
                $source_account->setTaxType(null);
                $source_account->setEnablePaymentsToAccount(null);
                $source_account->setShowInExpenseClaims(null);
                $source_account->setClass(null);
                $source_account->setReportingCode(null);
                $source_account->setReportingCodeName(null);

                // 'type' => 'setType',
                // 'bank_account_number' => 'setBankAccountNumber',
                // 'status' => 'setStatus',
                // 'description' => 'setDescription',
                // 'bank_account_type' => 'setBankAccountType',
                // 'currency_code' => 'setCurrencyCode',
                // 'system_account' => 'setSystemAccount',
                // 'has_attachments' => 'setHasAttachments',
                // 'updated_date_utc' => 'setUpdatedDateUtc',
                // 'add_to_watchlist' => 'setAddToWatchlist',
                // 'validation_errors' => 'setValidationErrors'

                // Create a new Account with the same data as the source account
                // $target_account = new \XeroAPI\XeroPHP\Models\Accounting\Account;
                

                // $target_account->setType($source_account->getType());
                // ... set other properties as needed ...

                // Create the account in the target tenant

                try {
                    $result = $xero->accountingApi->createAccount($xero->target_accounts_tenant_id, $source_account);
                } catch (\XeroAPI\XeroPHP\ApiException $e) {
                    $errors[] = json_decode($e->getResponseBody(), true);
                    // return ["http_code"=>400, "error"=>$error];
                }
                sleep(1);
            }

            return ["http_code"=>200,"result"=>$result,"errors"=>$errors];    

        // } catch (\XeroAPI\XeroPHP\ApiException $e) {
        //     $error = json_decode($e->getResponseBody(), true);
        //     return ["http_code"=>400, "error"=>$error];
        // }
    }
}