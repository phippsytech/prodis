<?php
namespace NDISmate\Models\DocumentType;

use RedBeanPHP\R as R;

class GetDocumentType
{
    public function __invoke($data)
    {
        $bean = R::load('documenttypes', $data['id']);

        if (!$bean) {
            throw new \Exception('Document Type Not Found', 404);
        } else {
            return $bean;
        }
    }
}
