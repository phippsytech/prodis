<?php

namespace NDISmate\Models\DocumentType;

use NDISmate\Utilities\ConvertFieldsToBoolean;

use \RedBeanPHP\R as R;

class ListDocumentTypes
{
    function __invoke($data)
    {
        
        $sql = "SELECT * FROM documenttypes";
        // Execute the prepared statement with the staff IDs as parameters
        $beans = R::getAll($sql);

        $beans = (new ConvertFieldsToBoolean)($beans, ['is_required']);

        return $beans;
    }
}
