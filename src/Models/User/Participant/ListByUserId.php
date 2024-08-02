<?php
namespace NDISmate\Models\User\Participant;

use \RedBeanPHP\R as R;

class ListByUserId
{
    public function __invoke($data)
    {
        if (!isset($data['user_id'])) {
            return ['http_code' => 400, 'error' => 'user_id is required'];
        }

        // this function returns an array of participant_ids that the user has access to

        $participant_ids = [];  // default is empty array (NO access to any participants)

        // look up user by id to see if they have full permission
        $access_all_participants = R::getCell('SELECT access_all_participants from users where id=:email', [':id' => $data['user_id']]);

        if ($access_all_participants) {
            // if user can access all participants get all participant_ids
            $participants = R::getAll(
                'SELECT participant_id from participants'
            );
        } else {
            // if user can't access all participants

            // get participant_ids from UserParticipantAccess table
            // are they staff?
            $staff = R::getRow('SELECT id from staffs where user_id=:user_id', [':user_id' => $user['id']]);

            if (isset($staff)) {
                // yes
                // get participant_ids from staff table
                $participants = R::getAll(
                    'SELECT client_id from clientstafs where staff_id=:staff_id',
                    [':staff_id' => $staff['id']]
                );

                // are they a supervisor?

                // yes
                // get participant_ids from staff table for each staff member they supervise
                // no
                // continue
            }

            // get any additional participant_ids from UserParticipantAccess where allowed is 1
        }

        // list any participant_ids where allowed is 0 (Deny access to these participants)
        $access_all_participants = R::getAll('SELECT participant_id from userparticipants where user_id=:user_id and allowed=0', [':user_id' => $data['user_id']]);

        return ['http_code' => HTTP_OK, 'result' => $beans];
    }
}
