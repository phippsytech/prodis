<?php

namespace NDISmate\Models\DocumentType;


use \RedBeanPHP\R as R;

class ListDocumentTypes
{
    function __invoke($data)
    {
        
        $sql = "SELECT * FROM documenttypes";
        // Execute the prepared statement with the staff IDs as parameters
        $beans = R::getAll($sql);

        return $beans;
    }
}
