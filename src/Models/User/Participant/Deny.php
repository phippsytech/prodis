<?php
namespace NDISmate\Models\User\Participant;

use \RedBeanPHP\R as R;

class Deny
{
    public function __invoke($data)
    {
        try {
            $bean = R::findOrCreate('userparticipants', ['user_id' => $data['user_id'], 'participant_id' => $data['participant_id']]);
            $bean->allowed = 0;
            R::store($bean);

            return true;
        } catch (\Exception $e) {
            // Throw the exception
            throw new \Exception($e->getMessage());
        }
    }
}
