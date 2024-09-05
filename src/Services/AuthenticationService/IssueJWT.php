<?php
namespace NDISmate\Services\AuthenticationService;

use Firebase\JWT\JWT;
use Hashids\Hashids;

final class IssueJWT
{
    public function __invoke($data)
    {
        $hashids = new Hashids(HASH_SALT, 8);

        $user_hash = $hashids->encode($data['user_id']);
        $jwt = JWT::encode([
            'iss' => 'Prodis',  // issuer
            // "aud" => "zero.fail", // audience
            'iat' => time(),
            'nbf' => time() - 60,
            'exp' => time() + SESSION_TIMEOUT,
            'user_hash' => $user_hash
        ], PRIVATE_KEY, 'RS256');

        return (string) $jwt;
    }
}
