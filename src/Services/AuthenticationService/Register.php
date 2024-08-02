<?php
namespace NDISmate\Services\AuthenticationService;

use NDISmate\CORE\JsonResponse;
use NDISmate\Models\User;
use RedBeanPHP\R as R;

final class Register
{
    public function __invoke($data)
    {
        $public_key = R::findOne('publickeys', ' public_key=:public_key ', [':public_key' => $data['public_key']]);

        // We only actually store this if the public key doesn't exist
        if (empty($public_key)) {
            // Create a new user (or return the existing one if it exists)
            // I am relying on the signed JWT to provide the email address
            $user = (new User)->create(['email' => $data['jwt_claims']->email]);

            $public_keys = R::dispense('publickeys');
            $public_keys->user = $user;
            $public_keys->public_key = $data['public_key'];
            R::store($public_keys);
        } else {
            // To avoid revealing that the public key already exists
            // Return ok.
            // The details won't be connected to an existing record.
            // CON: This can easily be detected by attempting to authorise with the public key after registration.
            // But it creates a smell that we can use to block from further activity
        }

        return JsonResponse::ok(['success' => true]);
    }
}
