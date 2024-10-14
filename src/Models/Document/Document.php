<?php
namespace NDISmate\Models;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Credential extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('credential', [
            'id' => [v::numericVal()],
            'name' => [v::stringType()],  // eg Behaviour Management Plan
            'description' => [v::stringType()],  // eg BMP
            'type' => [v::stringType()],  // eg 11_023_0110_7_3
            'date_collection_option' => [v::stringType()],  // issued, expiry, do_not_collect
            'years_until_expiry' => [v::numericVal()],  // only if date_collection_option is set to 'issued'
            'collect_from_therapist' => [v::stringType()],
            'collect_from_sil' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addCredential', 'create', true, ['admin']],
            ['listCredentials', 'getAll', true, []],
            ['listCredentialsByStaffIds', 'listCredentialsByStaffIds', true, []],
            ['getCredential', 'getOne', true, []],
            ['updateCredential', 'update', true, ['admin']],
            ['deleteCredential', 'deleteCredential', true, ['admin']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function listCredentialsByStaffIds($data)
    {
        return (new \NDISmate\Models\Credential\ListCredentialsByStaffIds)($data);
    }

    function deleteCredential($data, $fields, $guard)
    {
        $user_roles = $guard->roles;
        // check if the user_role is a super user.
        $is_super = false;
        if (count(array_intersect($user_roles, ['super'])) !== 0) {
            $is_super = true;
        }

        // Check if credential is in use by a staff member
        $result = R::getAll('SELECT * FROM staffcredentials WHERE credential_id=:credential_id',
            [':credential_id' => $data['id']]);

        if (count($result) !== 0) {
            if (!$is_super) {
                return ['http_code' => 400, 'error_message' => 'Credential is in use by a staff member'];
            } else {
                // Only super user has premission to delete credentials that are in use by staff
                // delete the staff credentials
                $result = R::exec('DELETE FROM staffcredentials WHERE credential_id=:credential_id',
                    [':credential_id' => $data['id']]);
            }
        }

        // Delete the credential
        $result = $this->delete($data);
        $result = $result['result'];
        return ['http_code' => 200];
    }
}
