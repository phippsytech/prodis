<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class User extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('users'));
        $this->fields = [
            'email' => [v::stringType()],
            'roles' => [v::optional(v::arrayType())],
            'name' => [v::optional(v::stringType())],
            'first_name' => [v::optional(v::stringType())],
            'last_name' => [v::optional(v::stringType())],
            'phone' => [v::optional(v::stringType())],
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
