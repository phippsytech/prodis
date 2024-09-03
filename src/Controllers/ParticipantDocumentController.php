<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Participant\Document\ListDocumentsByParticipantId;

use NDISmate\Services\ParticipantDocumentService\AddParticipantDocument;
use NDISmate\Services\ParticipantDocumentService\UpdateParticipantDocument;
use NDISmate\Services\ParticipantDocumentService\DeleteParticipantDocument;


final class ParticipantDocumentController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addParticipantDocument' => [new AddParticipantDocument, null, true, ['admin']],
            'updateParticipantDocument' => [new UpdateParticipantDocument, null, true, ['admin']],
            'deleteParticipantDocument' => [new DeleteParticipantDocument, null, true, ["sil.admin", "admin"]],
            'listDocumentsByParticipantId' => [new ListDocumentsByParticipantId, null, true, []],

        ];
    }
}
