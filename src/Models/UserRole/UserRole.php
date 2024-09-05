<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class UserRole extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('userroles'));
        $this->fields = [
            'user_id' => [v::numericVal()],
            'email' => [v::stringType()],
            'roles' => [v::optional(v::arrayType())],
            'access_all_participants' => [v::boolVal()]
        ];
    }

    public function create($data)
    {
        $user = R::findOne('users', ' email=:email ', [':email' => $data['email']]);

        if (empty($user)) {
            $user = parent::create($data);
        }

        return $user;
    }
}
