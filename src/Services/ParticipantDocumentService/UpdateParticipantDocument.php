<?php

namespace NDISmate\Services\ParticipantDocumentService;

use NDISmate\Models\Participant\Document\Document;
use NDISmate\Services\ParticipantDocumentService\UploadParticipantDocumentFile;

class UpdateParticipantDocument
{

    function __invoke($data)
    {

        try {

            if (empty($data['participant_id'])) throw new \Exception('A participant id must be provided');
            
           
            // if a file has been provided, upload it.
            if (!empty($data['base64_file'])) {
                
                $vultrStorageRef = (new UploadParticipantDocumentFile)([
                    'base64_file' => $data['base64_file'],
                    'participant_id' => $data['participant_id'],
                    'file_extension' => $data['file_extension'],
                    'document_date' => $data['document_date'],
                    'expired_at' => $data['expired_at']
                ]);

                $data['vultr_storage_ref'] = $vultrStorageRef;
            }
            unset($data['base64_file']);

            $result = (new Document)->update($data);    

            return $result;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
            return $e;
        }
    }
}
