<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Staff\Credential\GetCredential as GetStaffCredential;
use NDISmate\Services\StaffCredentialService\AddStaffCredential;
use NDISmate\Services\StaffCredentialService\UpdateStaffCredential;
use NDISmate\Services\StaffCredentialService\DeleteStaffCredential;

use \NDISmate\Models\Staff\Credential\ListCredentials;
use \NDISmate\Models\Staff\Credential\ListMissingCredentials;
use \NDISmate\Models\Staff\Credential\ListExpiredCredentials;
use \NDISmate\Models\Staff\Credential\ListDueCredentials;
use \NDISmate\Models\Staff\Credential\ListCredentialsByStaffId;
use \NDISmate\Models\Staff\Credential\ListStaffByCredentialId;


final class StaffCredentialController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addCredential' => [new AddStaffCredential, null, true, ['admin']],
            'updateCredential' => [new UpdateStaffCredential, null, true, ['admin']],
            'deleteCredential' => [new DeleteStaffCredential, null, true, ["sil.admin", "admin"]],
            'getCredential' => [new GetStaffCredential, null, true, []],
            'listCredentials' => [new ListCredentials, null, true, []],
            'listMissingCredentials' => [new ListMissingCredentials, null, true, []],
            'listExpiredCredentials' => [new ListExpiredCredentials, null, true, []],
            'listDueCredentials' => [new ListDueCredentials, null, true, []],
            'listCredentialsByStaffId' => [new ListCredentialsByStaffId, null, false, []],
            'listStaffByCredentialId' => [new ListStaffByCredentialId, null, false, []],

        ];
    }
}
