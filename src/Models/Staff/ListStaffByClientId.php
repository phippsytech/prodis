<?php
namespace NDISmate\Models\Staff\Credential;

use Respect\Validation\Validator as v;
use \NDISmate\CORE\CRUD;
use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class ListStaffByClientId
{
    function __invoke($data)
    {
        return ['http_code' => 400, 'error_message' => '\Staff\Credential\ListStaffByClientId has been deprecated.'];

        // $bean = R::getAll(
        //     'SELECT
        //         staffs.id,
        //         users.name,
        //         users.first_name,
        //         users.last_name,
        //         users.email,
        //         users.phone
        //     FROM staffs
        //     JOIN clientstaffs on (clientstaffs.staff_id = staffs.id)
        //     JOIN users on (staffs.user_id = users.id)
        //     WHERE clientstaffs.client_id=:client_id
        //         -- AND (archived is null or archived!=1)
        //         -- AND (
        //         --     clientstaffs.primary_therapist=1
        //             -- OR clientstaffs.primary_occupational_therapist=1
        //             -- OR clientstaffs.primary_speech_therapist=1
        //         -- )
        //     ',
        //     [':client_id' => $data['client_id']]
        // );

        // return ['http_code' => 200, 'result' => $bean];
    }
}
