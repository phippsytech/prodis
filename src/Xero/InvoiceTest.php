<?php

namespace NDISmate\Xero;


use NDISmate\Xero\Helpers as XeroHelpers;
// use BPMB\Models\Emailer as SMTP;
// use BPMB\Models\ContentTemplate;

class InvoiceTest {

    function createInvoice(){

        return (new XeroHelpers)->createInvoices([
            [
                "account_code" => 200,
                "due_date" => new \DateTime(date("Y-m-d")),
                "reference" => "GSI Registration",
                "contact_id"=> "58697449-85ef-46ae-83fc-6a9446f037fb",
                "invoice_number"=> "TEST-123",
                "line_items" => [
                    [
                        "account_code"=>"200",
                        "item_code"=>"PPSR-1",
                        "description"=>"GSI - Some Customer",
                        "quantity"=>1,
                        "unit_amount"=>1
                    ]
                ]
            ]
        ]);

    }


    // This is only here as reference for now
    // function getInvoiceAsPdf(){

    //     $result = (new XeroInternalHelpers)->getInvoiceAsPdf('5b45378f-5a34-4211-af99-0af004afdbbf');

    //     SMTP::queue([
    //         "to_name" => "phippsy@phippsy.tech",  
    //         "to_email" => "phippsy@phippsy.tech",  
    //         "from_name" => "Bulletproof My Business",
    //         "from_email" => GOOGLE_SENDER_EMAIL,
    //         "subject" => "Test email of invoice",
    //         "html" => (new ContentTemplate)->render([
    //             'template' => "email/bpmb-invoice.tpl", 
    //             'template_variables' => [],
    //         ]),
    //         // attachment contains an ARRAY of files
    //         "attachment" => [ 
    //             [
    //                 'filename' => "BPMB-TEST.pdf", 
    //                 'fileContent' => $result['pdf']
    //             ]
    //         ]
    //     ]);

    // }

}

