<?php
namespace NDISmate\Services\ObjectStorageService;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use NDISmate\CORE\EncryptionManager;
use Exception;

class listObjects
{
    public function __invoke($data, $fields, $guard, S3Client $s3)
    {
        try {
            $bucket = $data['bucket'];

            $result = $this->s3->listObjectsV2([
                'Bucket' => $bucket,
            ]);

            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $objectNames[] = $object['Key'];
                }
            }

            return $objectNames;
        } catch (S3Exception $e) {
            echo $e->getAwsErrorMessage() . ' ' . __FILE__ . ' ' . __LINE__;
            echo 'HTTP/1.1 500 Internal Server Error';
        } catch (Exception $e) {
            echo $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
            echo 'Error: ' . $e->getMessage() . "\n";
        }
    }
}
