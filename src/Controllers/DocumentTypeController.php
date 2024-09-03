<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\DocumentType;
use NDISmate\Models\DocumentType\GetDocumentType;
use NDISmate\Models\DocumentType\ListDocumentTypes;
use NDISmate\Services\DocumentTypeService\DeleteDocumentType;


final class DocumentTypeController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addDocumentType' => [new DocumentType, 'create', true, ['admin']],
            'listDocumentTypes' => [new ListDocumentTypes, null, true, []],
            'getDocumentType' => [new GetDocumentType, null, true, []],
            'updateDocumentType' => [new DocumentType, 'update', true, ['admin']],
            'deleteDocumentType' => [new DeleteDocumentType, null, true, ['admin']],
        ];
    }
}
