<?php

namespace NDISmate\Services\AuthenticationService;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\DomainException;
use Firebase\JWT\Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\InvalidArgumentException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\UnexpectedValueException;
use Hashids\Hashids;
use NDISmate\CORE\JsonResponse;
use NDISmate\CORE\UnauthorizedException;

final class Guard
{
    var $token;
    var $claims;
    var $roles;
    var $user_id;

    function __construct($token)
    {
        $this->token = $token;
        $this->user_id = null;
        $this->roles = [];

        try {
            $this->claims = JWT::decode($this->token, new Key(PUBLIC_KEY, 'RS256'));

            $hashids = new Hashids(HASH_SALT, 8);
            $this->user_id = $hashids->decode($this->claims->user_hash)[0];
            // $this->roles = (new \NDISmate\Models\User\GetRoles)(['user_id' => $this->user_id]);
        } catch (InvalidArgumentException $e) {
            // throw new \Exception($e->getMessage());
        } catch (DomainException $e) {
            // throw new \Exception($e->getMessage());
        } catch (UnexpectedValueException $e) {
            // throw new \Exception($e->getMessage());
        } catch (SignatureInvalidException $e) {
            // throw new \Exception($e->getMessage());
        } catch (BeforeValidException $e) {
            // throw new \Exception($e->getMessage());
        } catch (ExpiredException $e) {
            // throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            // throw new \Exception($e->getMessage());
        }
    }

    /**
     * I need to pass in staff_id and client_id
     *  Then check if staff_id / client_id has any role override
     *  Also, if the staff_id has access to the client_id
     *  Also Teams - A team leader may have access to staff and their clients
     *  However - in the case of Dean, I don't want him to have access to all clients
     */
    public function protect($roles = [])
    {
        try {
            $this->claims = JWT::decode($this->token, new Key(PUBLIC_KEY, 'RS256'));

            $hashids = new Hashids(HASH_SALT, 8);

            $this->user_id = null;
            $decodedUserHash = null;

            // if (!isset($this->claims->user_hash)) {
            //     throw new \Exception('Insufficient permission.');
            // }

            // $decodedUserHash = $hashids->decode($this->claims->user_hash);
            // if (empty($decodedUserHash)) {
            //     throw new \Exception('Insufficient permission.');
            // }

            // $this->user_id = $decodedUserHash[0];

            $this->user_id = $hashids->decode($this->claims->user_hash)[0];

            $this->roles = (new \NDISmate\Models\UserRole\GetRoles)(['user_id' => $this->user_id], null, $this);

            if ($roles == [])
                return true;  // if no additional role is required, proceed

            // If a role is required, check the current user has that role
            if ((new \NDISmate\Models\UserRole\CheckRoles)(['user_id' => $this->user_id, 'roles' => $roles], null, $this)) {
                return true;
            }

            // if we got here, we don't have permission
            throw new UnauthorizedException('insufficient permission');
        } catch (InvalidArgumentException $e) {
            throw new UnauthorizedException($e->getMessage());
        } catch (DomainException $e) {
            throw new UnauthorizedException($e->getMessage());
        } catch (UnexpectedValueException $e) {
            throw new UnauthorizedException($e->getMessage());
        } catch (SignatureInvalidException $e) {
            throw new UnauthorizedException($e->getMessage());
        } catch (BeforeValidException $e) {
            throw new UnauthorizedException($e->getMessage());
        } catch (ExpiredException $e) {
            throw new UnauthorizedException($e->getMessage());
        } catch (\Exception $e) {
            throw new UnauthorizedException($e->getMessage());
        }
    }
}
