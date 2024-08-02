<?php

namespace NDISmate\Xero;


use NDISmate\Xero\Session;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


// Use this class to deserialize error caught
use \XeroAPI\XeroPHP\AccountingObjectSerializer;

class Helpers {

    var $session;
    var $tenant_id;  
    var $accountingApi;

    
    function __construct(){

            $this->session = new Session();    
            
            $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$this->session->getSession()['token'] );

            $this->accountingApi = new \XeroAPI\XeroPHP\Api\AccountingApi(
                new \GuzzleHttp\Client(),
                $config
            );

            $this->payrollAuApi = new \XeroAPI\XeroPHP\Api\PayrollAuApi(
                new \GuzzleHttp\Client(),
                $config
            );

            $this->tenant_id = (string) KeyValue::get('xero_tenant_id');
            $this->payroll_tenant_id = (string) KeyValue::get('xero_payroll_tenant_id');

    }

    // this is an alias for getAccountingTenantId
    function getTenantId(){
        return $this->getAccountingTenantId();
    }

    function getAccountingTenantId(){
        // for now I need to use xero_tenant_id, but I intende to change this to xero_accounting_tenant_id in future
        $xero_tenant_id = KeyValue::get('xero_tenant_id');
        if (empty($xero_tenant_id)) return ["http_code"=>404, "error_message"=>"Tenant ID not found"];
        return ["http_code"=>200, "result"=>$xero_tenant_id];
    }

    function getPayrollTenantId(){
        $xero_payroll_tenant_id = KeyValue::get('xero_payroll_tenant_id');
        if (empty($xero_payroll_tenant_id)) return ["http_code"=>404, "error_message"=>"Payroll Tenant ID not found"];
        return ["http_code"=>200, "result"=>$xero_payroll_tenant_id];
    }


    function setAccountingTenantId($data){

        $tenants = $this->listTenants();

        $key = array_search($data['accounting_tenant_id'], array_column($tenants, 'id'));

        if ($key !== false) {
            // found the matching tenant
            KeyValue::set('xero_tenant_id', $data['accounting_tenant_id']);
            return ["http_code"=>201, "result"=>$data['accounting_tenant_id']];		
            
        } else {
            // tenant not found
            return ["http_code"=>400, "error_message"=>"A valid Tenant ID was not found.  Could not set Tenant ID."];
        }
        
    }


    function setPayrollTenantId($data){

        $tenants = $this->listTenants();

        $key = array_search($data['payroll_tenant_id'], array_column($tenants, 'id'));

        if ($key !== false) {
            // found the matching tenant
            KeyValue::set('xero_payroll_tenant_id', $data['payroll_tenant_id']);
            return ["http_code"=>201, "result"=>$data['payroll_tenant_id']];		
            
        } else {
            // tenant not found
            return ["http_code"=>400, "error_message"=>"A valid Payroll Tenant ID was not found.  Could not set Payroll Tenant ID."];
        }
        
    }

    function getPayrollSettings(){
        return (new \NDISmate\Xero\Payroll\GetSettings)($this);       
    }

	function listTenants(){

		$config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$this->session->getSession()['token'] );
		
		$identityApi = new \XeroAPI\XeroPHP\Api\IdentityApi(
			new \GuzzleHttp\Client(),
			$config
		);  

		$connections = $identityApi->getConnections();

		foreach($connections as $connection){
			$tenants[]=[
				"id"=>$connection->getTenantId(),
				"name"=>$connection->getTenantName(),
				"type"=>$connection->getTenantType()
			];
		}

		return $tenants;

	}

    function createInvoices($invoices){
        return (new \NDISmate\Xero\CreateInvoices)($this, $invoices);       
    }



    function getSuperannuationFunds(){
        return (new \NDISmate\Xero\GetSuperannuationFunds)($this);        
    }



    function getAccounts(){
        return (new \NDISmate\Xero\GetAccounts)($this);        
    }

    function getBankTransactions(){
        return (new \NDISmate\Xero\GetBankTransactions)($this);        
    }


    function getEmployee($data){
        return (new \NDISmate\Xero\Staff\GetEmployee)($this, $data);
    }

    function getPayRun(){
        return (new \NDISmate\Xero\GetPayRun)($this);
    }
    
    function getPayRuns(){
        return (new \NDISmate\Xero\GetPayRuns)($this);
    }
    
    function makeBatchPayment(){
        return (new \NDISmate\Xero\MakeBatchPayment)($this);        
    }

    function deleteBatchPayment(){
        return (new \NDISmate\Xero\DeleteBatchPayment)($this);        
    }

    
    function setupItems(){
        return (new \NDISmate\Xero\SetupItems)($this);
    }


    function findOrCreateXeroContactByEmail($data){
        return (new \NDISmate\Xero\FindOrCreateXeroContactByEmail)($this, $data);
    }

    function findOrCreateXeroContactByPlanManagerId($data){
        return (new \NDISmate\Xero\FindOrCreateXeroContactByPlanManagerId)($this, $data);
    }

    function getEmployees($data){
        return (new \NDISmate\Xero\GetEmployees)($this, $data);
    }

    function updatePlanManagersWithXeroContactId(){
        return (new \NDISmate\Xero\UpdatePlanManagersWithXeroContactId)($this);
    }


    function getContacts(){

        try {

            $apiResponse = $this->accountingApi->getContacts($this->tenant_id, null, null, null, null, null, null, true, null);
            // $pdf = base64_encode($apiResponse->fread($apiResponse->getSize()));
            return ["http_code"=>200, "result"=>$apiResponse];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
        
    }


    function setNDIAContact($xero_contact_id){
        KeyValue::set("xero_ndia_contact_ref",$xero_contact_id);
        return ["http_code"=>200];
    }


    function getNDIAContact(){

        $xero_contact_id = KeyValue::get("xero_ndia_contact_ref");
        if(!empty($xero_contact_id)) {
            return ["http_code"=>200, "xero_contact_id"=>$xero_contact_id];
        }
        return ["http_code"=>404, "error"=>"NDIA Contact ID not found"];
        
    }


    function getInvoice($data){
        return (new \NDISmate\Xero\GetInvoice)($this, $data);
    }



    function getInvoiceAsPdf($invoice_id){

        try {
            $apiResponse = $this->accountingApi->getInvoiceAsPdf($this->tenant_id, $invoice_id, "application/pdf");
            $pdf = base64_encode($apiResponse->fread($apiResponse->getSize()));
            return ["http_code"=>200, "pdf"=>$pdf];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
        
    }


    function getInvoiceById($invoice_id){

        try {
            $apiResponse = $this->accountingApi->getInvoice($this->tenant_id, $invoice_id);
            return ["http_code"=>200, "result"=>$apiResponse];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
        
    }

    
}