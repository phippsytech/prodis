<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class DeleteTimeline {

    function __invoke($data) {
        // Delete timeline by id

        // unset($data['jwt_claims']);

        // Check if the _id field is set in the data
        if (isset($data['id'])) {
            // Delete the record with the given _id
            $id = $data['id'];
            // if (is_array($id)) {
            //     $id = implode('', $id);
            // }

            // Load the bean
            $timeline = R::load('timelines', $id);

            // Check if the bean exists
            if (!is_null($timeline)) {
                // Trash the bean
                $result = R::trash($timeline);
                return $result;
            } else {
                throw new \Exception("Timeline not found", 404);
            }
        } else {
            throw new \Exception("Missing id field", 400);
        }
    }
}