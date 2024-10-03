<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class GetComplaint {

    function __invoke($data)
    {
    
        $complaint = R::load('complaints', $data['id']);

        return $complaint;
    }
}