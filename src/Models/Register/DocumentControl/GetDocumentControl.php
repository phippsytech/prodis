<?php
namespace NDISmate\Models\Register\DocumentControl;


use \RedBeanPHP\R as R;

class GetDocumentControl
{
    public function __invoke($data)
    {
        $bean = R::load('documentcontrols', $data['id']);

        return $bean;
    }
}