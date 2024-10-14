<?php

namespace NDISmate\Models\Register;
use \RedBeanPHP\R as R;

class GetConflictOfInterests
{
    function __invoke($data) {


        $sql = "SELECT coi.*, 
            s.name AS staff_name
            FROM conflictofinterests coi
            LEFT JOIN staffs s ON coi.staff_id = s.id";

        
        $result = R::getAll($sql);

        return $result;
    }
}