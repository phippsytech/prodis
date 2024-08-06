<?php
namespace NDISmate\Services\ObjectStorageService;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Exception;
use \RedBeanPHP\R as R;
use NDISmate\GoogleAPI\Drive;
use NDISmate\Services\ObjectStorageService\PutS3Object;

class MigrateFromGoogleDrive
{
    public function __invoke($data, $fields, $guard, S3Client $s3)
    {

        try {
            
            // get google drive id
            $colValues = R::getCol('SELECT google_drive_file_ref FROM staffcredentials where google_drive_file_ref is not null');

            if (count($colValues) > 0) {

                foreach ($colValues as $value ) {

                    // $googleFile = (new Drive())->getFile(['file_id' => $value]);

                    $drive = new Drive;
                    $fileContent = $drive->getFileContents(['file_id' => $value]);

                    // Extract the file name from the file path
                    $fileName = $drive->getMetaData($value);

                    // upload to vultr
                    $result = (new PutS3Object)([
                        'bucket' => VULTR_BUCKET,
                        'key' => $fileName,
                        'fileContent' => $fileContent
                    ],
                        null, null, $s3);
                    // save the id to the table

                    // R::exec('SELECT * FROM staffcredentials WHERE ')

                    return $result;
                }
            }
            
            // $buckets = $s3->listBuckets();
            // foreach ($buckets['Buckets'] as $bucket) {
            //     $bucketArray[] = $bucket;
            // }
            // return $bucketArray;
        } catch (S3Exception $e) {
            throw new Exception($e->getAwsErrorMessage() . ' ' . __FILE__ . ' ' . __LINE__);
        } catch (Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
