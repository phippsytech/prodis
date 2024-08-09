<?php
namespace NDISmate\ObjectStorage;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use NDISmate\ObjectStorage\VultS3\PutS3Object;

class VultrS3
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

    function upload($file, $key)
    {
        try {
            $result = (new PutS3Object)([
                'bucket' => VULTR_BUCKET,
                'key' => $key,
                'fileContent' => $file
            ],
                null, null, $this->s3);
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
            echo 'Error: ' . $e->getMessage() . "\n";
        }
    }

    function testUpload()
    {
        try {
            $fileContent = file_get_contents(__DIR__ . '/crystals.png');
            $result = (new PutS3Object)([
                'bucket' => VULTR_BUCKET,
                'key' => 'crystals.png',
                'fileContent' => $fileContent
            ],
                null, null, $this->s3);
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
            echo 'Error: ' . $e->getMessage() . "\n";
        }
    }

    function getFile()
    {
        try {
            $result = $this->getS3Object(
                bucket: VULTR_BUCKET,
                key: 'crystals.png'
            );
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
            echo 'Error: ' . $e->getMessage() . "\n";
        }
    }

    function generatev4GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
