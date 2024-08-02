<?php
namespace NDISmate\Models\User\Participant;

use \RedBeanPHP\R as R;

class Delete
{
    public function __invoke($data)
    {
        try {
            $bean = R::findOne('userparticipants', ' user_id=:user_id AND participant_id=:participant_id AND allowed=:allowed ', [
                ':user_id' => $data['user_id'],
                ':participant_id' => $data['participant_id'],
                ':allowed' => $data['allowed']
            ]);
            if ($bean)
                R::trash($bean);

            return true;
        } catch (\Exception $e) {
            // Throw the exception
            throw new \Exception($e->getMessage());
        }
    }
}
