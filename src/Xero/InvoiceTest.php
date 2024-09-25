<?php

namespace NDISmate\Xero;


use NDISmate\Xero\Helpers as XeroHelpers;
// use BPMB\Models\Emailer as SMTP;
// use BPMB\Models\ContentTemplate;

class InvoiceTest
{

    function createInvoice()
    {

        return (new XeroHelpers)->createInvoices([
            [
                "account_code" => 200,
                "due_date" => new \DateTime(date("Y-m-d")),
                "reference" => "GSI Registration",
                "contact_id" => "58697449-85ef-46ae-83fc-6a9446f037fb",
                "invoice_number" => "TEST-123",
                "line_items" => [
                    [
                        "account_code" => "200",
                        "item_code" => "PPSR-1",
                        "description" => "GSI - Some Customer",
                        "quantity" => 1,
                        "unit_amount" => 1
                    ]
                ]
            ]
        ]);
    }
}
