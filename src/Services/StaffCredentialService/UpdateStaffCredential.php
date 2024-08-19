<?php

namespace NDISmate\Services\StaffCredentialService;

use NDISmate\Models\Staff\Credential as StaffCredential;
use NDISmate\Services\StaffCredentialService\UploadStaffCredentialFile;

class UpdateStaffCredential
{

    function __invoke($data)
    {

        try {

            if (empty($data['staff_id'])) throw new \Exception('A staff id must be provided');
            if (empty($data['credential_id'])) throw new \Exception('A credential id must be provided');

            // if a file has been provided, upload it.
            if (!empty($data['base64_file'])) {

                $vultrStorageRef = (new UploadStaffCredentialFile)([
                    'base64_file' => $data['base64_file'],
                    'staff_id' => $data['staff_id'],
                    'credential_id' => $data['credential_id'],
                    'file_extension' => $data['file_extension']
                ]);

                $data['vultr_storage_ref'] = $vultrStorageRef;
            }
            unset($data['base64_file']);

            $result = (new StaffCredential)->update($data);

            return $result;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
            return $e;
        }
    }
}
