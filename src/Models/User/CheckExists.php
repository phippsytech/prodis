<?php
namespace NDISmate\Models\User;

use \RedBeanPHP\R as R;

class CheckExists
{
    public function __invoke($data)
    {
        $exists = R::findOne('users', ' id=:user_id ', [':user_id' => $data['user_id']]);
        return $exists ? true : false;
    }
}
