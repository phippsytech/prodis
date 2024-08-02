<?php
namespace NDISmate\Services\ObjectStorageService;

use Exception;
use \Aws\S3\Exception\S3Exception;
use \Aws\S3\S3Client;

class CreateS3Bucket
{
    public function __invoke($data, $fields, $guard, S3Client $s3)
    {
        try {
            $result = $s3->createBucket([
                'Bucket' => $bucketName,
            ]);
        } catch (S3Exception $e) {
            throw new Exception($e->getAwsErrorMessage() . ' ' . __FILE__ . ' ' . __LINE__);
        } catch (Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
