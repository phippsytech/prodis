<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

//create or update timeline if exist
class SaveTimeline {

    function __invoke($data) {
        
        try {
            // unset($data['jwt_claims']);
            var_dump($data);
            if (isset($data['id'])) {   

                $timeline = R::findOne('timelines', 'id = ?', [$data['id']]); 
           
                if (!is_null($timeline)) {
                    $ids = $data['_id'];
        
                    unset ($data['_id']);
                
                    if ($is_array($id)) {
                        $ids = implode('', $ids);
                    }
        
                    $id = R::store(json_encode($data));
        
                    $result = R::findOne('timelines', 'id = ?', [$id]);

                    if (!is_null($result) ) {
                        $result->form_data = json_decode($result->form_data);
                    }

                    return $result;
                    
                } 
            } else {
                print_r('paolo');
                // Insert new record
                $timeline = R::dispense('timelines');
                foreach ($data as $key => $value) {
                    if ($key === 'form_data') {
                        $timeline->$key = json_encode($value);
                        continue;
                    }
                    $timeline->$key = $value;
                }
    
                $id =  R::store($timeline);
                var_dump($result);

        
               $result = R::findOne('timelines', 'id = ?', [$id]);

                if (!is_null($result) ) {
                    $result->form_data = json_decode($result->form_data);
                }

               return $result;
    
            }
            
        } catch (\Exception $e) {
            echo 'test';
            throw new \Exception($e->getMessage());
        }
       

    }
}

