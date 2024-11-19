<?php
namespace NDISmate\Models\User\Participant;

use \RedBeanPHP\R as R;

class ListAllowedByUserId
{
    public function __invoke($data)
    {
     
        try {
            $beans = R::getAll('SELECT 
            userparticipants.participant_id as id, 
            clients.id as client_id, 
            clients.name as client_name , 
            clients.name as name , 
            clients.archived, 
            clients.on_hold, 
            clients.ndis_number
            from userparticipants 
            join clients on (clients.id = userparticipants.participant_id) 
            where userparticipants.user_id=:user_id 
            and userparticipants.allowed=1', 
            [
                ':user_id' => $data['user_id']
            ]
        );
            return $beans;
        } catch (\Exception $e) {
            // Throw the exception
            throw new \Exception($e->getMessage());
        }
    }
}
