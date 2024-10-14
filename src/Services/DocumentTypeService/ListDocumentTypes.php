<?php

namespace NDISmate\Services\DocumentTypeService;

use \RedBeanPHP\R as R;

class ListDocumentTypes {

    public function __invoke() {

        $bean = R::getAll('SELECT * FROM documenttypes');
        return $bean;
    }
}