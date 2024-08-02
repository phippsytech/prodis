<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class GetInvoiceBatchDate {

    public function __invoke($data) {

        $invoice_batch_date = R::getCell( 
            'SELECT     
                generation_date
                FROM invoicebatchs
                where id=:id
            ',
            [ 
                ":id"=>$data['invoice_batch']
            ]
        );
    
        return ["http_code"=>200, "result"=>$invoice_batch_date];
        
    }
}