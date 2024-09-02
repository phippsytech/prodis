<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetTimeline {

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

           $timeline = R::findOne('id', 'id=:id', [':id' => $data['id']] );

           if ($timeline) {

                if ($timeline['report_type'] == "End of Shift") $timeline['report_type'] = "EndOfShift";
                if ($timeline['report_type'] == "Sleep Disturbance") $timeline['report_type'] = "SleepDisturbance";
                if ($timeline['report_type'] == "Sleep Time") $timeline['report_type'] = "SleepTime";
                if ($timeline['report_type'] == "Risk Assessment") $timeline['report_type'] = "RiskAssessment";
           }

           return JsonResponse::ok($timeline);

        } catch(\Exception $e) {
            return JsonResponse::notFound();
        }
    }
}