<?php

namespace NDISmate\Services\ParticipantDocumentService;

use NDISmate\Models\Participant\Document as ParticipantDocument;

class DeleteParticipantDocument
{

    function __invoke($data)
    {

        $deleteResult = (new ParticipantDocument)->delete($data);

        if (isset($deleteResult['vultr_storage_ref'])) {
            try {
                // TODO: Need to implement deleteFile function
                // deleteFile(["key" => $deleteResult['vultr_storage_ref']]);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return;
    }
}
