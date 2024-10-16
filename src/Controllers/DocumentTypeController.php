<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\DocumentType;
use NDISmate\Models\DocumentType\GetDocumentType;
use NDISmate\Services\DocumentTypeService\ListDocumentTypes;
use NDISmate\Services\DocumentTypeService\DeleteDocumentType;


final class DocumentTypeController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'listDocumentTypes' => [new ListDocumentTypes, null, true, []],
            'deleteDocumentType' => [new DeleteDocumentType, null, true, ['admin']],
        ];
    }
}
