<?php
namespace NDISmate\Services\AuthenticationService;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\IssueJWT;
use NDISmate\Services\AuthenticationService\Verify;
use RedBeanPHP\R as R;

final class Authenticate
{
    public function __invoke($data)
    {
        // NOTE: it is possible (but improbable) that 2 identical challenges could be created for different users.

        // look up challenge string
        $challenge = R::findOne('challenges',
            ' challenge=:challenge ',
            [':challenge' => $data['challenge']]);

        $public_key_bean = R::load('publickeys', $challenge->publickey_id);

        // get user_public_key and user_id
        $user_id = $public_key_bean->user_id;
        $user_public_key = $public_key_bean->public_key;

        $verification_result = (new Verify)([
            'challenge' => $data['challenge'],
            'signature' => $data['signature'],
            'user_public_key' => $user_public_key,
        ]);

        // the challenge should be deleted immediately regardless of the outcome.
        R::trash($challenge);

        if ($verification_result) {
            $user = R::load('users', $user_id);
            return JsonResponse::ok(['jwt' => (new IssueJWT)(['user_id' => $user_id])]);
        } else {
            return JsonResponse::forbidden(['error_message' => 'Authentication failed.']);
        }
    }
}
