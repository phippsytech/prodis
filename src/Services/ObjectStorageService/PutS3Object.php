<?php
namespace NDISmate\Services\ObjectStorageService;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use NDISmate\CORE\EncryptionManager;
use Exception;

class PutS3Object
{
    public function __invoke($data, $fields, $guard, S3Client $s3)
    {
        try {

            $bucket = $data['bucket'];
            $key = $data['key'];
            $fileContent = $data['fileContent'];

            if ($fileContent === false) {
                throw new Exception('Failed to read the file');
            }

            $encryptionManager = new EncryptionManager(VULTR_S3_ENCRYPTION_KEY);
            $fileContent = $encryptionManager->encryptData($fileContent);

            $result = $s3->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => $fileContent,  // this is the raw data.  You do not need to convert it.
                'ACL' => 'private'
            ]);
            
            echo "Object URL: " . $result['ObjectURL'] . "\n";
            return $result['ObjectURL'];
        } catch (S3Exception $e) {
            echo $e->getAwsErrorMessage() . ' ' . __FILE__ . ' ' . __LINE__;
            echo 'HTTP/1.1 500 Internal Server Error';
        } catch (Exception $e) {
            echo $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
            echo 'Error: ' . $e->getMessage() . "\n";
        }
    }
}
