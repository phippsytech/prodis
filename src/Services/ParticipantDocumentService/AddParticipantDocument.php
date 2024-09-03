<?php

namespace NDISmate\Services\ParticipantDocumentService;

use NDISmate\Models\Participant\Document as ParticipantDocument;
use NDISmate\Services\ParticipantDocumentService\UploadParticipantDocumentFile;

class AddParticipantDocument
{

    function __invoke($data)
    {

        try {

            if (empty($data['participant_id'])) throw new \Exception('A participant id must be provided');
            if (empty($data['documenttype_id'])) throw new \Exception('A document type id must be provided');

            // if a file has been provided, upload it.
            if (!empty($data['base64_file'])) {
                $vultrStorageRef = (new UploadParticipantDocumentFile)([
                    'base64_file' => $data['base64_file'],
                    'participant_id' => $data['participant_id'],
                    'documenttype_id' => $data['documenttype_id'],
                    'file_extension' => $data['file_extension']
                ]);

                $data['vultr_storage_ref'] = $vultrStorageRef;
            }

            $result = (new ParticipantDocument)->create($data);

            return $result;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
