<?php

namespace NDISmate\Services\StaffCredentialService;

use \RedBeanPHP\R as R;
use NDISmate\Services\ObjectStorageService\PutS3Object;
use Aws\S3\S3Client;

class UploadStaffCredentialFile
{

    function __invoke($data)
    {
        try {
            if (empty($data['base64_file'])) throw new \Exception('A file must be uploaded');
            if (empty($data['staff_id'])) throw new \Exception('A staff id must be provided');
            if (empty($data['credential_id'])) throw new \Exception('A credential id must be provided');
            if (empty($data['file_extension'])) throw new \Exception('A file extension must be provided');

            print("doing the upload");

            $s3 = new S3Client([
                'version' => 'latest',
                'region' => 'us-east-1',
                'endpoint' => 'https://' . S3_HOST_NAME,
                'credentials' => [
                    'key' => VULTR_ACCESS_KEY,
                    'secret' => VULTR_SECRET_KEY,
                ]
            ]);

            // get the staffer name
            $staffName = R::getCell('SELECT name FROM staffs WHERE id=:staff_id', [":staff_id" => $data['staff_id']]);

            // get the credential name
            $credentialName = R::getCell('SELECT name FROM credentials WHERE id=:id', [":id" => $data['credential_id']]);




            // Split the string at the first comma
            $parts = explode(',', $data['base64_file']);

            // The second part contains the actual base64 string
            $base64String = $parts[1];


            $fileContent = base64_decode($base64String);
            $fileName = date("Y-m-d") . "_" . $staffName . "_" . $credentialName;

            if ($data['file_extension']) {
                $fileName .= "." . $data['file_extension'];
            }

            $md5Hash = md5($fileContent);
            $fileName = $md5Hash . '-' . $fileName;

            (new PutS3Object)([
                'bucket' => VULTR_BUCKET,
                'key' => $fileName,
                'fileContent' => $fileContent
            ], null, null, $s3);

            return $fileName;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
