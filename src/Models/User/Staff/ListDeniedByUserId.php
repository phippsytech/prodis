<?php
namespace NDISmate\Models\User\Participant;

use \RedBeanPHP\R as R;

class ListDeniedByUserId
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll('SELECT userparticipants.participant_id as id, clients.name from userparticipants join clients on (clients.id = userparticipants.participant_id) where userparticipants.user_id=:user_id and userparticipants.allowed=0', [':user_id' => $data['user_id']]);

            return $beans;
        } catch (\Exception $e) {
            // Throw the exception
            throw new \Exception($e->getMessage());
        }
    }
}
