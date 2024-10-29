<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Form;
use NDISmate\Models\Form\GetForm;

class FormController extends BaseController {

    public function defineController() {
        
        $this->controller = [
            'createForm' => [new Form, 'createForm', false, []],
            'updateForm' => [new Form, 'updateForm', false, []],
            'deleteForm' => [new Form, 'deleteForm', false, []],
            'getForm' => [new GetForm, null, false, []],
            'listForms' => [new Form, 'listForms', false, []],
        ];
    }
}