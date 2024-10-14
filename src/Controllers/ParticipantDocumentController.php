<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Participant\Document\ListDocumentsByParticipantId;

use NDISmate\Services\ParticipantDocumentService\UpdateParticipantDocument;
use NDISmate\Services\ParticipantDocumentService\DeleteParticipantDocument;
use NDISmate\Models\Participant\Document\GetDocument;
use NDISmate\Services\ParticipantDocumentService\AddParticipantDocument;
use  NDISmate\Models\Participant\Document\ListParticipantByDocumentId;
use NDISmate\Models\Participant\Document\ListDueDocuments;
use NDISmate\Models\Participant\Document\ListExpiredDocuments;

final class ParticipantDocumentController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'getDocument' => [new GetDocument, null, true, ['admin']],
            'addParticipantDocument' => [new AddParticipantDocument, null, true, ['admin']],
            'updateParticipantDocument' => [new UpdateParticipantDocument, null, true, ['admin']],
            'deleteParticipantDocument' => [new DeleteParticipantDocument, null, true, ["sil.admin", "admin"]],
            'listDocumentsByParticipantId' => [new ListDocumentsByParticipantId, null, true, []],
            'listParticipantByDocumentId' => [new ListParticipantByDocumentId, null, true, []],
            'listDueDocuments' => [new ListDueDocuments, null, true, ["admin"]],
            'listExpiredDocuments' => [new ListExpiredDocuments, null, true, ["admin"]],

        ];
    }
}
