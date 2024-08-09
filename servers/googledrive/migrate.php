<?php
require '/var/www/prodis/init.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Exception;
use RedBeanPHP\R as R;
use NDISmate\GoogleAPI\Drive;
use NDISmate\Services\ObjectStorageService\PutS3Object;


$s3 = new S3Client([
    'version' => 'latest',
    'region' => 'us-east-1',
    'endpoint' => 'https://' . S3_HOST_NAME,
    'credentials' => [
        'key' => VULTR_ACCESS_KEY,
        'secret' => VULTR_SECRET_KEY,
    ]
]);

$connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASSWORD, RABBITMQ_VHOST);
$channel = $connection->channel();

$channel->queue_declare('google_drive_migration', false, true, false, false);
$channel->basic_consume('google_drive_migration', '', false, false, false, false, function ($message) use ($channel, $s3) {
    
    // Acknowledge the message to remove it from the queue
    // I am doing this to prevent an endless loop if the worker crashes.
    $channel->basic_ack($message->delivery_info['delivery_tag']);

    $unserializedData = unserialize($message->body);
    $data = $unserializedData;

    try {

        // do the job here
        $value = $data['google_drive_file_ref'];
        $table = $data['table'];

        $drive = new Drive();
        $fileContent = $drive->getFileContents(['file_id' => $value]);
        $fileName = $drive->getMetaData($value);
        $md5Hash = md5($fileContent);
        $fileName = $md5Hash . '-' . $fileName;

        $results[] = (new PutS3Object)([
            'bucket' => VULTR_BUCKET,
            'key' => $fileName,
            'fileContent' => $fileContent
        ], null, null, $s3);

        $credential = R::findOne($table, ' google_drive_file_ref = ? ', [$value]);
        if (!empty($credential)) {
            $credential->vultr_storage_ref = $fileName;
            R::store($credential);
        }

    } catch (\Exception $e) {
        // TODO: This should flag an error
    }

});

// echo "Waiting for messages...\n";

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
