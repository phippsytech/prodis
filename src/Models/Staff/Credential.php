<?php
namespace NDISmate\Models\Staff;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;
// use NDISmate\CORE\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Credential extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("staffcredential", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'credential_id' => [v::numericVal()],
            'details' => [v::stringType()], // eg Cert 4 in Disability support
            'google_drive_file_ref' => [v::stringType()], 
            'credential_date' => [v::stringType()]
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addCredential" , "addCredential", true, [] ],
            ["getCredential", "getCredential", true, [] ],
            ["listCredentials", "listCredentials", true, [] ],
            ["listMissingCredentials", "listMissingCredentials", true, [] ],
            ["listExpiredCredentials", "listExpiredCredentials", true, [] ],
            ["listDueCredentials", "listDueCredentials", true, [] ],
            ["listCredentialsByStaffId", "listCredentialsByStaffId", false, [] ],
            ["listStaffByCredentialId", "listStaffByCredentialId", false, [] ],
            ["updateCredential", "updateCredential", true, [] ],
            ["deleteCredential", "deleteCredential", true, ["sil.admin", "admin"] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }


    // Additional Methods and overrides here:

    function addCredential($data){
        return (new \NDISmate\Models\Staff\Credential\AddCredential)($data);
    }

    function deleteCredential($data){
        return (new \NDISmate\Models\Staff\Credential\DeleteCredential)($data);
    }

    function updateCredential($data){
        return (new \NDISmate\Models\Staff\Credential\UpdateCredential)($data);
    }

    function getCredential($data){
        return (new \NDISmate\Models\Staff\Credential\GetCredential)($data);
    }
 
    function listCredentials($data){
        return (new \NDISmate\Models\Staff\Credential\ListCredentials)($data);
    }

    function listMissingCredentials($data){
        return (new \NDISmate\Models\Staff\Credential\ListMissingCredentials)($data);
    }

    function listExpiredCredentials($data){
        return (new \NDISmate\Models\Staff\Credential\ListExpiredCredentials)($data);
    }

    function listDueCredentials($data){
        return (new \NDISmate\Models\Staff\Credential\ListDueCredentials)($data);
    }

    function listCredentialsByStaffId($data){
        return (new \NDISmate\Models\Staff\Credential\ListCredentialsByStaffId)($data);
    }

    function listStaffByCredentialId($data){
        return (new \NDISmate\Models\Staff\Credential\ListStaffByCredentialId)($data);
    }

}