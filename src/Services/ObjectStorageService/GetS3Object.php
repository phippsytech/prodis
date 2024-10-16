<?php
namespace NDISmate\Services\ObjectStorageService;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use NDISmate\CORE\EncryptionManager;
use Exception;

class GetS3Object
{
    public function __invoke($data, $fields, $guard, S3Client $s3)
    {
        try {
          
            $bucket = $data['bucket'];
            $key = $data['key'];

            $result = $s3->getObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            $encryptionManager = new EncryptionManager(VULTR_S3_ENCRYPTION_KEY);
            $fileContent = $encryptionManager->decryptData($result['Body']->getContents());
            
            // Return the object content
            return base64_encode($fileContent);
        } catch (S3Exception $e) {
                throw new Exception($e->getAwsErrorMessage() . ' ' . __FILE__ . ' ' . __LINE__);
        } catch (Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
