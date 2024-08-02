<?php

// namespace NDISmate\Xero;


// use \RedBeanPHP\R as R;


// class SetupTherapistTrackingCategories {

//     function __invoke($obj){

//         try {
//             $apiResponse = $obj->accountingApi->getTrackingCategories($obj->tenant_id, "Name==\"Therapist\"");
    
//             $staffs = R::getAll( 'SELECT * FROM staffs where archived is null or archived!=1');
//             foreach($staffs as $staff){
//                 if(!empty($staff['name']))
//                     $staff_names[] = trim($staff['name']);
//             }
    
//             $tracking_category = $apiResponse->getTrackingCategories();
            
//             // if tracking category is empty, create it
//             if (empty($tracking_category)){
//                 $tracking_category_obj = new \XeroAPI\XeroPHP\Models\Accounting\TrackingCategory;
//                 $tracking_category_obj->setName("Therapist");
//                 $create_result = $obj->accountingApi->createTrackingCategory($obj->tenant_id,$tracking_category_obj);

//                 // now get the tracking categories again so we can work with them
//                 $tracking_category = $create_result->getTrackingCategories();
//             }

//             $tracking_category=$tracking_category[0];

//             $tracking_category_id = $tracking_category["tracking_category_id"];

//             $tracking_options=[];
//             foreach ($tracking_category->getOptions() as $option) {
//                 $tracking_options[]= trim($option->getName());
//             }

//             $result = array_diff($staff_names, $tracking_options);

//             if (!empty($result)){
//                 foreach($result as $result_code_key=>$result_code_value){
//                     $obj_option = new \XeroAPI\XeroPHP\Models\Accounting\TrackingOption;
//                     $obj_option->setName($result_code_value);
//                     $result = $obj->accountingApi->createTrackingOptions($obj->tenant_id,$tracking_category_id, $obj_option);
//                 }
//             }

//             return ["http_code"=>200];    

//         } catch (\XeroAPI\XeroPHP\ApiException $e) {
//             $error = json_decode($e->getResponseBody(), true);
//             return ["http_code"=>400, "error"=>$error];
//         }
        
//     }

// }
