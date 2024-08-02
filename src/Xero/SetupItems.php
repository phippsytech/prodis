<?php

namespace NDISmate\Xero;


use \RedBeanPHP\R as R;


class SetupItems {

    function __invoke($obj){

        try {
            $apiResponse = $obj->accountingApi->getItems($obj->tenant_id);

            $services = R::getAll( 'SELECT * FROM services');
            foreach($services as $service){
                if(!empty($service['code']))
                    $service_codes[] = $service['code'];
            }

            // Add TRAVEL as a service code because it isn't technically considered a "service"
            $service_codes[] = "TRAVEL";
            
            $items = $apiResponse->getItems();

            $item_codes=[];
            if (!empty($items)){
                foreach ($items as $item) {
                    if(!empty($item->getCode()))
                        $item_codes[]= $item->getCode();
                }
            }

            $result = array_diff($service_codes, $item_codes);

            if(!empty($result)){
                $obj_items = new \XeroAPI\XeroPHP\Models\Accounting\Items;
                foreach($result as $result_code_key=>$result_code_value){
                    $obj_item = new \XeroAPI\XeroPHP\Models\Accounting\Item;
                    $obj_item->setCode($result_code_value);
                    $arr_items[]=$obj_item;
                }
                $obj_items->setItems($arr_items);
                $result = $obj->accountingApi->createItems($obj->tenant_id, $obj_items);
            }

            return ["http_code"=>200];

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
        
    }

}