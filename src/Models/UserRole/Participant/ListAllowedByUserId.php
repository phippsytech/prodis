<?php
namespace NDISmate\Models\UserRole\Participant;

use \RedBeanPHP\R as R;

class ListAllowedByUserId
{
    public function __invoke($data)
    {
        try {
            $beans = R::getAll('SELECT userparticipants.participant_id as id, clients.name from userparticipants join clients on (clients.id = userparticipants.participant_id) where userparticipants.user_id=:user_id and userparticipants.allowed=1', [':user_id' => $data['user_id']]);

            return $beans;
        } catch (\Exception $e) {
            // Throw the exception
            throw new \Exception($e->getMessage());
        }
    }
}
