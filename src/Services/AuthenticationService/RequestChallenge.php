<?php
namespace NDISmate\Services\AuthenticationService;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

final class RequestChallenge
{
    public function __invoke($data)
    {
        $public_key = $data['public_key'];

        // load the user
        $public_key_bean = R::findOne('publickeys', ' public_key=:public_key', ['public_key' => $public_key]);

        if ($public_key_bean) {
            // generate challenge
            $challenge = base64_encode(openssl_random_pseudo_bytes(128));

            // store user and challenge;
            $challenges = R::dispense('challenges');
            $challenges->publickey = $public_key_bean;
            $challenges->challenge = $challenge;

            R::store($challenges);

            // send challenge string to client
            return JsonResponse::ok(['challenge' => $challenge]);
        }

        // if we don't rate limit this, it will be abused.
        return JsonResponse::notFound();
    }
}
