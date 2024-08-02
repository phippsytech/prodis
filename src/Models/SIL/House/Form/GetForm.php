<?php
namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;


class GetForm{

    function __invoke($data){
        // this try catch prevents the controller from sending a 401 error if an error happens in search
        try{
            $mongo_db = new \MongoDB\Client("mongodb://localhost:27017",[
                "username" => MONGODB_USER,
                "password" => MONGODB_PASSWORD,
                "authSource"  => MONGODB_DATABASE
            ]);

            $forms = $mongo_db->crystelcare->forms;

            $form = $forms->findOne([
                "_id" => new \MongoDB\BSON\ObjectId($data['id'])
            ]);
            
            if ($form) {


                // rename some report types
                if ($form['report_type'] == "End of Shift") $form['report_type'] = "EndOfShift";
                if ($form['report_type'] == "Sleep Disturbance") $form['report_type'] = "SleepDisturbance";
                if ($form['report_type'] == "Sleep Time") $form['report_type'] = "SleepTime";
                if ($form['report_type'] == "Risk Assessment") $form['report_type'] = "RiskAssessment";

                // form found, perform operations with the form
                return JsonResponse::ok($form);

            } else {
                // form not found
                return JsonResponse::notFound();
            }
        } catch( \Exception $e){
            return JsonResponse::notFound();
        }

    }

}

