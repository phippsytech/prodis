<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \NDISmate\CORE\KeyValue;
use \NDISmate\Xero\Session;

class Xero
{
    var $tenant_id;
    var $accountingApi;

    function __construct()
    {
        $session = new Session();
        $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken((string) $session->getSession()['token']);

        $this->accountingApi = new \XeroAPI\XeroPHP\Api\AccountingApi(
            new \GuzzleHttp\Client(),
            $config
        );

        $this->tenant_id = (string) KeyValue::get('xero_tenant_id');
    }
}
