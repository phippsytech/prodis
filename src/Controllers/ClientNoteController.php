<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Client\Note\GetNote;
use NDISmate\Models\Client\Note\ListNotes;
use NDISmate\Models\Client\Note\ListNotesByClientId;
use NDISmate\Models\Client\Note;

final class ClientNoteController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addNote' => [new Note, 'addNote', true, ['participant.modify']],
            'updateNote' => [new Note, 'update', true, ['participant.modify']],
            'getNote' => [new GetNote, null, true, []],
            'listNotes' => [new ListNotes, null, true, []],
            'listNotesByClientId' => [new ListNotesByClientId, null, true, []],
        ];
    }
}
