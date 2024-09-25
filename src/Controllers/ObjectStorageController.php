<?php

namespace NDISmate\Controllers;

use Aws\S3\S3Client;
use NDISmate\CORE\BaseController;
use NDISmate\Services\ObjectStorageService\CreateS3Bucket;
use NDISmate\Services\ObjectStorageService\GetS3Object;
use NDISmate\Services\ObjectStorageService\ListBuckets;
use NDISmate\Services\ObjectStorageService\ListObjects;
use NDISmate\Services\ObjectStorageService\PutS3Object;
use NDISmate\Services\ObjectStorageService\GetS3ObjectFile;

final class ObjectStorageController extends BaseController
{
    var $s3;

    public function __construct()
    {
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => 'https://' . S3_HOST_NAME,
            'credentials' => [
                'key' => VULTR_ACCESS_KEY,
                'secret' => VULTR_SECRET_KEY,
            ]
        ]);
    }

    protected function defineController()
    {
        $this->controller = [
            'createBucket' => [new CreateS3Bucket, null, false, [], [$this->s3]],
            'getS3Object' => [new GetS3Object, null, true, [], [$this->s3]],
            'listBuckets' => [new ListBuckets, null, false, [], [$this->s3]],
            'listObjects' => [new ListObjects, null, false, [], [$this->s3]],
            'putS3Object' => [new PutS3Object, null, false, [], [$this->s3]],
            'getS3ObjectFile' => [new GetS3ObjectFile, null, false, [], [$this->s3]]

        ];
    }
}
