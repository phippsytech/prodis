<?php
namespace NDISmate\Services\ObjectStorageService;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use NDISmate\CORE\EncryptionManager;
use Exception;
use NDISmate\Services\ObjectStorageService\GetS3Object;

class GetS3ObjectFile
{
    public function __invoke($data, $fields, $guard, S3Client $s3)
    {
        try {
          
            $key = $data['key'];

            $result = (new GetS3Object)([
                'bucket' => VULTR_BUCKET,
                'key' => $key,
                
            ],
                null, null, $s3);

            return $result;

        } catch (S3Exception $e) {
            throw new Exception($e->getAwsErrorMessage() . ' ' . __FILE__ . ' ' . __LINE__);
        } catch (Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
