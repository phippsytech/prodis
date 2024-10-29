<?php

namespace NDISmate\Models\Form;

use DateTime;
use Exception;
use \RedBeanPHP\R as R;

class CreateForm  {

    public function __invoke($data) {

        $form = R::dispense('forms');
        
        $form->title = $data['title'];
        $form->description = $data['description'];
        $form->current_schema_id = null;
        $form->created_at = new DateTime();

        $formId = R::store($form);

        if (!$formId) throw new Exception("Form Not Found");

        $beanForm = R::findOne('forms', 'id = :id', [':id' => $formId]);

        return [
            'form_id' => $beanForm->id,
            'title' => $beanForm->title,
            'description' => $beanForm->description,
            'created_at' => $beanForm->created_at,
        ];
    }
}