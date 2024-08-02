<?php
namespace NDISmate\Models;

use NDISmate\Xero\Helpers as XeroHelpers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Invoice extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('invoicebatch', [
            'id' => [v::numericVal()],
            'generation_date' => [v::stringType()],  // this is a date YYYY-MM-DD
            'type' => [v::stringType()],  // NDIA or Managed
        ]);

        // action, class method, guard, roles
        $this->actions = [
            // Invoice Batch
            ['generateInvoiceBatch', 'generateInvoiceBatch', true, ['accounts']],
            ['generateInvoicesInXero', 'generateInvoicesInXero', true, ['accounts']],
            ['listInvoiceBatches', 'listInvoiceBatches', true, ['accounts']],
            ['listInvoiceLineItems', 'listInvoiceLineItems', true, ['accounts']],
            ['listGroupedInvoiceLineItems', 'listGroupedInvoiceLineItems', true, ['accounts']],
            ['listInvoiceAggregatedLineItems', 'listInvoiceAggregatedLineItems', true, ['accounts']],
            ['listUnbilled', 'listUnbilled', true, ['accounts']],
            ['getNDIABulkCSV', 'getNDIABulkCSV', true, ['accounts']],
            ['getInvoiceBatch', 'getInvoiceBatch', true, ['accounts']],
            // Client Invoices
            ['listClientInvoices', 'listClientInvoices', true, ['stakeholder', 'accounts']],
            // ['getClientInvoiceSummary', 'getClientInvoiceSummary', true, ['stakeholder', 'accounts']],
            // Invoice
            ['getInvoice', 'getInvoice', false, ['accounts']],
            ['getInvoiceBatchDate', 'getInvoiceBatchDate', false, ['accounts']],
            ['reverseInvoice', 'reverseInvoice', true, ['accounts']],
            ['testReverseXeroInvoiceLineItems', 'testReverseXeroInvoiceLineItems', true, ['accounts']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function generateInvoiceBatch($data)
    {
        $result = (new \NDISmate\Models\Invoice\GenerateInvoiceBatch)($this, $data);

        if ($result['http_code'] != 200)
            return $result;

        $invoice_data['invoice_batch'] = $result['invoice_batch'];

        $invoice_result = (new \NDISmate\Models\Invoice\GenerateInvoicesInXero)($invoice_data);

        $invoices = $invoice_result['result'];
        return [
            'http_code' => 201,
            'result' => [
                'invoices' => $invoices,
                'invoice_batch' => $result['invoice_batch']
            ]
        ];
    }

    function generateInvoicesInXero($data)
    {
        return (new \NDISmate\Models\Invoice\GenerateInvoicesInXero)($data);
    }

    function listInvoiceLineItems($data)
    {
        return (new \NDISmate\Models\Invoice\ListInvoiceLineItems)($data);
    }

    function listInvoiceAggregatedLineItems($data)
    {
        return (new \NDISmate\Models\Invoice\ListInvoiceAggregatedLineItems)($data);
    }

    function listGroupedInvoiceLineItems($data)
    {
        return (new \NDISmate\Models\Invoice\ListGroupedInvoiceLineItems)($data);
    }

    function listClientInvoices($data)
    {
        return (new \NDISmate\Models\Invoice\ListClientInvoices)($data);
    }

    // function getClientInvoiceSummary($data)
    // {
    //     return (new \NDISmate\Models\Invoice\GetClientInvoiceSummary)($data);
    // }

    function getInvoiceBatch($data)
    {
        return (new \NDISmate\Models\Invoice\GetInvoiceBatch)($data);
    }

    function listInvoiceBatches($data)
    {
        return (new \NDISmate\Models\Invoice\ListInvoiceBatches)($data);
    }

    function listUnbilled($data)
    {
        $data['invoice_batch'] = null;
        return (new \NDISmate\Models\Invoice\ListUnbilledLineItems)($data);
    }

    // #########
    // Invoice #
    // #########

    function getInvoice($data)
    {
        return (new \NDISmate\Models\Invoice\GetInvoice)($data);
    }

    function getInvoiceBatchDate($data)
    {
        return (new \NDISmate\Models\Invoice\GetInvoiceBatchDate)($data);
    }

    function reverseInvoice($data)
    {
        return (new \NDISmate\Models\Invoice\ReverseInvoice)($data);
    }

    function getNDIABulkCSV($data)
    {
        return (new \NDISmate\Models\Invoice\GetNDIABulkCSV)($data);
    }

    // ###########
    // # Testing #
    // ###########

    function testReverseXeroInvoiceLineItems($data)
    {
        return (new \NDISmate\Models\Invoice\TestReverseXeroInvoiceLineItems)($data);
    }
}
