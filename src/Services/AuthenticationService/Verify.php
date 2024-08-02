<?php
namespace NDISmate\Services\AuthenticationService;

final Class Verify
{
    public function __invoke($data)
    {
        $challenge = $data['challenge'];
        $signature = $data['signature'];
        $user_public_key = $data['user_public_key'];

        $verification_result = openssl_verify($challenge, base64_decode($signature), $user_public_key, OPENSSL_ALGO_SHA256);

        if ($verification_result === 1)
            return true;

        return false;
    }
}
