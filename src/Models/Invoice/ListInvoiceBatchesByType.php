<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class ListInvoiceBatchesByType {

    public function __invoke($data) {

        $bean = R::getAll( 
            'SELECT     
                id,
                generation_date
                FROM invoicebatchs
                WHERE type=:type
            ORDER BY generation_date desc, id desc',
            [ 
                ":type"=>$data['type']
            ]
        );
    
        return ["http_code"=>200, "result"=>$bean];
        
    }
}