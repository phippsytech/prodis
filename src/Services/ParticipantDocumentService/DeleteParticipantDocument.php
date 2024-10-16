<?php

namespace NDISmate\Services\ParticipantDocumentService;

use NDISmate\Models\Participant\Document\Document as ParticipantDocument;
use Aws\S3\S3Client;
class DeleteParticipantDocument
{

    function __invoke($data)
    {

        $deleteResult = (new ParticipantDocument)->delete($data);

        if (isset($deleteResult['vultr_storage_ref'])) {
            try {
                // TODO: Need to implement deleteFile function
                // deleteFile(["key" => $deleteResult['vultr_storage_ref']]);

                $s3 = new S3Client([
                    'version' => 'latest',
                    'region' => 'us-east-1',
                    'endpoint' => 'https://' . S3_HOST_NAME,
                    'credentials' => [
                        'key' => VULTR_ACCESS_KEY,
                        'secret' => VULTR_SECRET_KEY,
                    ]
                ]);


                $result = $s3Client->deleteObject([
                    'Bucket' => VULTR_BUCKET,
                    'Key'    => $data['vultr_storage_ref'],
                ]);
                return $result ? 1 :0;
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return;
    }
}
