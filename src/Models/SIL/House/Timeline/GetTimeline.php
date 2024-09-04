<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetTimeline {

    function __invoke($data) {
  
        try {
            // get the timeline  from the timeline table
            // use readbean for queries

           $timeline = R::findOne('timelines', 'id=:id', [':id' => $data['id']] );

           if (!$timeline) throw new \Exception("Timeline not found", 404);

              
           if (isset($timeline->form_data)) {
                $timeline->form_data = json_decode($timeline->form_data, true);
            }

            if ($timeline['report_type'] == "End of Shift") $timeline['report_type'] = "EndOfShift";
            if ($timeline['report_type'] == "Sleep Disturbance") $timeline['report_type'] = "SleepDisturbance";
            if ($timeline['report_type'] == "Sleep Time") $timeline['report_type'] = "SleepTime";
            if ($timeline['report_type'] == "Risk Assessment") $timeline['report_type'] = "RiskAssessment";

            return $timeline;

        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}