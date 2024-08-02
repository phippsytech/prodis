<?php
namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;


class DeleteForm {
    function __invoke($data) {
        unset($data['jwt_claims']);

        $mongo_db = new \MongoDB\Client("mongodb://localhost:27017", [
            "username" => MONGODB_USER,
            "password" => MONGODB_PASSWORD,
            "authSource"  => MONGODB_DATABASE
        ]);

        $forms = $mongo_db->crystelcare->forms;

        // Check if the _id field is set in the data
        if (isset($data['_id'])) {
            // Delete the record with the given _id
            $id = $data['_id'];
            if (is_array($id)) {
                $id = implode('', $id);
            }
            $result = $forms->deleteOne(['_id' => new \MongoDB\BSON\ObjectID($id)]);
            return JsonResponse::ok(["result" => $result]);
        } else {
            // Return an error if the _id field is not set
            return JsonResponse::error("Missing _id field");
        }
    }
}