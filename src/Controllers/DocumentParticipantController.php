<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Document\DocumentParticipant;

final class DocumentParticipantController extends BaseController {

    protected function defineController(){
        
        $this->controller  = [
            'addDocumentParticipant' => [ new DocumentParticipant, 'createDocumentParticipant', null, true , []],
            'listByDocumentId' => [ new DocumentParticipant, 'listByDocumentId', null, true , []]
        ];
    }
}