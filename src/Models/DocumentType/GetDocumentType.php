<?php
namespace NDISmate\Models\DocumentType;

use NDISmate\Utilities\ConvertFieldsToBoolean;

use RedBeanPHP\R as R;

class GetDocumentType
{
    public function __invoke($data)
    {
        $bean = R::load('documenttypes', $data['id']);

        if (!$bean) {
            throw new \Exception('Document Type Not Found', 404);
        } else {
            $bean = (new ConvertFieldsToBoolean)($bean, ['is_required']);
            return $bean;
        }
    }
}
