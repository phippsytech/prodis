<?php
namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;


class SaveForm {
    function __invoke($data) {
        unset($data['jwt_claims']);

        $mongo_db = new \MongoDB\Client('mongodb://'.MONGODB_HOST.':'.MONGODB_PORT, [
            'username' => MONGODB_USER,
            'password' => MONGODB_PASSWORD,
            'authSource' => MONGODB_AUTHSOURCE
        ]);

       

        $forms = $mongo_db->crystelcare->forms; 

        // Check if the _id field is set in the data
        if (isset($data['_id'])) {
            // Create an update query to update the record
            $id = $data['_id'];
            unset($data['_id']); // Remove _id field from data
            $updateQuery = ['$set' => $data];
            if (is_array($id)) {
                $id = implode('', $id);
            }
            $result = $forms->updateOne(['_id' => new \MongoDB\BSON\ObjectID($id)], $updateQuery);



            return JsonResponse::ok(["result" => $result]);
        } else {
            // Insert a new record if the _id field is not set
            $result = $forms->insertOne($data);
            return JsonResponse::ok(["result" => $result]);
        }
    }
}