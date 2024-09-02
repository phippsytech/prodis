<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class DeleteTimeline {

    function __invoke($data) {
        // Delete timeline by id

        unset($data['jwt_claims']);

        // Check if the _id field is set in the data
        if (isset($data['_id'])) {
            // Delete the record with the given _id
            $id = $data['_id'];
            if (is_array($id)) {
                $id = implode('', $id);
            }

            // Load the bean
            $timeline = R::load('timeline', $id);

            // Check if the bean exists
            if ($timeline->id) {
                // Trash the bean
                R::trash($timeline);
                return JsonResponse::ok(["result" => "Record deleted successfully"]);
            } else {
                return JsonResponse::error("Record not found");
            }
        } else {
            // Return an error if the _id field is not set
            return JsonResponse::error("Missing _id field");
        }
    }
}