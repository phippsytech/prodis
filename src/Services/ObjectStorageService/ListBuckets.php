<?php
namespace NDISmate\Services\ObjectStorageService;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Exception;

class ListBuckets
{
    public function __invoke($data, $fields, $guard, S3Client $s3)
    {
        try {
            $buckets = $s3->listBuckets();
            foreach ($buckets['Buckets'] as $bucket) {
                $bucketArray[] = $bucket;
            }
            return $bucketArray;
        } catch (S3Exception $e) {
            throw new Exception($e->getAwsErrorMessage() . ' ' . __FILE__ . ' ' . __LINE__);
        } catch (Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
