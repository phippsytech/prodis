<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\DocumentControl;

final class DocumentControlController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addDocumentControl' => [new DocumentControl, 'create', true, []],
            'updateDocumentControl' => [new DocumentControl, 'update', true, []],
            'deleteDocumentControl' => [new DocumentControl, 'delete', true, []],
        ];
    }
}