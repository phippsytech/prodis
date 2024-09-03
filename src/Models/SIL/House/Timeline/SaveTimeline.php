<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

//create or update timeline if exist
class SaveTimeline {

    function __invoke($data) {

        try {
            echo 'test';
            // unset($data['jwt_claims']);
            
            $timeline = R::findOne('timeline', 'id = ?', [$data['id']]); 
            print_r($data);
            if (!is_null($timeline)) {
                $ids = $data['_id'];
    
                unset ($data['_id']);
             
                if ($is_array($id)) {
                    $ids = implode('', $ids);
                }
    
                $result = R::store(json_encode($data));
    
                return JsonResponse::ok(["result" => R::findOne('timeline', 'id = ?', [$result])]);
                
            } else {
                
                // Insert new record
                $timeline = R::dispense('timeline');
                foreach ($data as $key => $value) {
                    $timeline->$key = $value;
                }
    
                $result =  R::store($timeline);
    
                return JsonResponse::ok(["result" => R::findOne('timeline', 'id = ?', [$result])]);
    
            }
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
       

    }
}

