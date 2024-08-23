<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetForm {

    function __invoke($data) {
        //TODO: assess what columns are needed for the timeline table
        // id
        // client_id
        //form_data ?? saved as JSON 
        // created_at
        // updated_at
        
        try {
            // get the timeline  from the timeline table
            // use readbean for queries

            // use data['id'] to get the timeline - client id


        } catch(\Exception $e) {
            return JsonResponse::notFound();
        }
    }
}