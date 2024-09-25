<?php

namespace NDISmate\Services\StaffCredentialService;

use NDISmate\Models\Staff\Credential as StaffCredential;

class DeleteStaffCredential
{

    function __invoke($data)
    {

        $deleteResult = (new StaffCredential)->delete($data);


        if (isset($deleteResult['vultr_storage_ref'])) {
            try {
                // TODO: Need to implement deleteFile function
                // deleteFile(["key" => $deleteResult['vultr_storage_ref']]);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return;
    }
}
