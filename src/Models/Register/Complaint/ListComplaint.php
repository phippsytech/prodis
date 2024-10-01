<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class ListComplaint {

    function __invoke($data) {

       $complaints =  R::findAll('complaints');

       return array_values($complaints);
    }
}