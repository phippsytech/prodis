<?php
namespace NDISmate\Services\AuthenticationService;

use Firebase\JWT\JWT;

final class IssueRegistrationToken
{
    public function __invoke($data)
    {
        $jwt = JWT::encode([
            'iss' => 'NDISmate Auth',  // issuer
            'iat' => time(),
            'exp' => time() + SESSION_TIMEOUT + 300,
            'email' => $data['email'],
        ], PRIVATE_KEY, 'RS256');

        return (string) $jwt;
    }
}
